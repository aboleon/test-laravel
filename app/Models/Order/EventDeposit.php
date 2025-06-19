<?php

namespace App\Models\Order;

use App\Accessors\EventDepositAccessor;
use App\Enum\EventDepositStatus;
use App\Enum\OrderOrigin;
use App\Enum\OrderStatus;
use App\Enum\PaymentCallState;
use App\Enum\PaymentMethod;
use App\Interfaces\CustomPaymentInterface;
use App\Mail\EventDepositPaymentResponse;
use App\Mail\EventMailDeposit;
use App\Mail\MailerMail;
use App\Models\CustomPaymentCall;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\EventManager\Grant\Grant;
use App\Models\EventManager\Sellable;
use App\Models\Order;
use App\Models\PayboxReimbursementRequest;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Services\PaymentProvider\PayBox\Paybox;
use App\Services\PaymentProvider\PayBox\TransactionRequest;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Mail;
use MetaFramework\Casts\PriceInteger;
use MetaFramework\Traits\Ajax;
use Throwable;

/**
 * @property CustomPaymentCall $paymentCall
 * @property string            $status Enum\DeventDepositStatus
 */
class EventDeposit extends Model implements CustomPaymentInterface
{
    use HasFactory;

    use Ajax;


    protected $table = 'event_deposits';
    protected $guarded = [];
    protected $casts
        = [
            'total_net'     => PriceInteger::class,
            'total_vat'     => PriceInteger::class,
            'reimbursed_at' => "datetime",
        ];
    public EventDepositAccessor $accessor;

    public function setAccessor(): void
    {
        $this->accessor = new EventDepositAccessor($this);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function eventContact(): BelongsTo
    {
        return $this->belongsTo(EventContact::class, 'event_contact_id');
    }

    public function sellable(): BelongsTo
    {
        return $this->belongsTo(Sellable::class, 'shoppable_id');
    }

    public function paymentCall(): MorphOne
    {
        return $this->morphOne(CustomPaymentCall::class, 'shoppable');
    }

    public function renderCustomPaymentForm(): Renderable
    {
        # generate new UUID
        $this->paymentCall->updateUuid()->save();

        $paybox = null;

        // Payment form
        if ($this->paymentCall->state !== PaymentCallState::SUCCESS->value) {
            $paybox = (new Paybox())
                ->setOrderable($this->paymentCall)
                ->fetchAvailableServer()
                ->setCustomPaymentRoutes()
                ->generateForm();
        }

        $this->setAccessor();

        return view('event-deposits.custom-payment-form')->with([
            'accessor'    => $this->accessor,
            'data'        => $this,
            'payform'     => $paybox,
            'paymentCall' => $this->paymentCall,
        ]);
    }

    public function paymentStateMessage(): string
    {
        $changeLocale  = false;
        $currentLocale = app()->getLocale();
        if ($currentLocale !== $this->accessor->locale()) {
            $changeLocale = true;
            app()->setLocale($this->accessor->locale());
        }
        $message = __('ui.payment_state.'.$this->paymentCall->state);

        if ($changeLocale) {
            app()->setLocale($currentLocale);
        }

        return $message;
    }

    public function processSuccessCustomPayment(): void
    {
        // Deposit
        $this->status           = EventDepositStatus::PAID->value;
        $this->paybox_num_trans = TransactionRequest::transactionId();
        $this->paybox_num_appel = TransactionRequest::callId();
        $this->save();
        $this->responseSuccess("Event Deposit updated");

        # Order
        $this->order->paybox_num_trans = TransactionRequest::transactionId();
        $this->order->paybox_num_appel = TransactionRequest::callId();
        $this->order->status = OrderStatus::PAID->value;
        $this->order->save();
        $this->responseSuccess("Order updated");

        # Transaction

        $this->paymentCall->transaction()->save(
            (new PaymentTransaction([
                'transaction_call_id' => TransactionRequest::callId(),
                'transaction_id'      => TransactionRequest::transactionId(),
                'return_code'         => TransactionRequest::returnCode(),
                'details'             => request()->all(),
            ])),
        );

        $this->responseSuccess("Transaction stored");

        # Payment
        $this->order->payments()->save(
            (new Payment([
                'order_id'           => $this->order->id,
                'date'               => now(),
                'payment_method'     => PaymentMethod::CB_PAYBOX->value,
                'transaction_id'     => $this->paymentCall->transaction->id,
                'amount'             => $this->total_net + $this->total_vat,
                'transaction_origin' => OrderOrigin::BACK->value,
            ])),
        );
        $this->responseSuccess("Payment stored");
    }

    public function sendPaymentMail(CustomPaymentCall $paymentCall): array
    {
        $this->ajaxMode();

        try {
            Mail::send(
                new MailerMail(
                    new EventMailDeposit($paymentCall),
                ),
            );

            $this->responseSuccess("La demande de paiement a été envoyée.");
        } catch (Throwable $e) {
            $this->responseException($e, "La demande de paiement n'a pas pu être envoyée.");
        }

        return $this->fetchResponse();
    }

    public function sendPaymentResponseNotification(CustomPaymentCall $paymentCall): array
    {
        try {
            Mail::send(
                new MailerMail(
                    new EventDepositPaymentResponse($paymentCall),
                ),
            );
            $this->responseSuccess("Mail envoyée avec succes.");
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->fetchResponse();
    }

    public function getEventContact(): EventContact
    {
        return $this->eventContact;
    }


    /**
     * @return MorphMany
     * Refund requests to Paybox
     */
    public function refundRequests(): MorphMany
    {
        return $this->morphMany(PayboxReimbursementRequest::class, 'shoppable');
    }

    public function shoppable(): BelongsTo
    {
        return match($this->shoppable_type) {
            'grantdeposit' => $this->belongsTo(Grant::class, 'shoppable_id', 'id'),
            default => $this->belongsTo(Sellable::class, 'shoppable_id', 'id'),
        };
    }


}
