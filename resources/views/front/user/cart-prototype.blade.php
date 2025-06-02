@php
    $account = $user->account;
@endphp
<x-front-logged-in-layout :event="$event">


    <x-front.session-notifs />

    <div class="container" x-data="{page: 'cart', paymentMethod: 'cb'}">
        <h3 class="mb-4 p-2 bg-primary-subtle rounded-1">{{__('My Cart - reservations')}}</h3>


        <div class="row g-4 g-sm-5">
            <div x-show="page==='cart'" class="col-lg-8 mb-4 mb-sm-0">
                <div class="card card-body border p-4 shadow">


                    <div class="row">
                        <div class="alert alert-dark d-flex align-items-center fade show py-3 pe-2"
                             role="alert">
                            <i class="bi bi-house-fill fa-fw me-1"></i>
                            Hébergement
                        </div>
                    </div>


                    @for($i=1; $i<=1; $i++)
                        <div class="card row mb-2">
                            <div class="card-body border">
                                <div class="row border-bottom border-top border-light-subtle">
                                    <div class="col-4 text-bg-light text-body">Intitulé</div>
                                    <div class="col-8 text-dark">Catégorie type</div>
                                </div>
                                <div class="row border-bottom border-light-subtle">
                                    <div class="col-4 text-bg-light text-body">Dates</div>
                                    <div class="col-8 text-dark">Du 09/09/2022 au 12/09/2022<br>3
                                        nuits
                                    </div>
                                </div>
                                <div class="row border-bottom border-light-subtle">
                                    <div class="col-4 text-bg-light text-body">Prix</div>
                                    <div class="col-8 text-dark">750€</div>
                                </div>
                                <div class="row border-bottom border-light-subtle">
                                    <div class="col-4 text-bg-light text-body">Frais de
                                        dossier
                                    </div>
                                    <div class="col-8 text-dark">10€</div>
                                </div>
                                <div class="row border-bottom border-light-subtle">
                                    <div class="col-4 text-bg-light text-body">Prix total</div>
                                    <div class="col-8 text-dark">760€</div>
                                </div>
                                <div class="row border-bottom border-light-subtle">
                                    <div class="col-4 text-bg-light text-body">Nombre de
                                        personnes
                                    </div>
                                    <div class="col-8 text-dark">2</div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-2">
                                    <a class="btn btn-danger btn-sm" href="#">Supprimer</a>
                                </div>
                            </div>
                        </div>
                    @endfor

                    <div class="row mb-3">
                        <div class="card border">
                            <div class="p-2">
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="flexRadioDefault"
                                               id="payment_type1">
                                        <label class="form-check-label" for="payment_type1">Régler
                                            un
                                            acompte (30% du montant + frais dossier) soit
                                            260€</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="radio"
                                               name="flexRadioDefault"
                                               id="payment_type2">
                                        <label class="form-check-label" for="payment_type2">Régler
                                            le
                                            montant total</label>
                                    </div>
                                </div>
                                <p class="small">
                                    Le solde devra être réglé avant le [J-1 mois avant date
                                    évènément]
                                </p>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="alert alert-dark d-flex align-items-center fade show py-3 pe-2"
                             role="alert">
                            <i class="bi bi-person-lines-fill fa-fw me-1"></i>
                            Prestations
                        </div>
                    </div>


                    <div class="d-block">
                        @for($i=1; $i<=3; $i++)
                            <div class="card row mb-2">
                                <div class="card-body border">
                                    <div class="row border-bottom border-top border-light-subtle">
                                        <div class="col-4 text-bg-light text-body">Intitulé</div>
                                        <div class="col-8 text-dark">Badge full congress</div>
                                    </div>
                                    <div class="row border-bottom border-light-subtle">
                                        <div class="col-4 text-bg-light text-body">Dates</div>
                                        <div class="col-8 text-dark">10/09/2022 à 20h</div>
                                    </div>
                                    <div class="row border-bottom border-light-subtle">
                                        <div class="col-4 text-bg-light text-body">Prix</div>
                                        <div class="col-8 text-dark">350€</div>
                                    </div>
                                    <div class="row border-bottom border-light-subtle">
                                        <div class="col-4 text-bg-light text-body pt-1">Quantité
                                        </div>
                                        <div class="col-8 text-dark">
                                            <input type="number"
                                                   value="1"
                                                   class="form-control form-control-sm w-auto">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2 mt-2 p-2">
                                        <a class="btn btn-danger btn-sm" href="#">Supprimer</a>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>

                    <div class="row justify-content-center mt-3">
                        <div @click="page='payment'" class="btn btn-primary w-auto">Finaliser la
                            commande
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="page==='payment'" class="col-lg-8 mb-4 mb-sm-0">
                <div class="card">

                    <div class="card-header bg-primary-subtle">
                        <h4 class="mb-0">Paiement</h4>
                    </div>
                    <div class="card-body border">


                        <div class="form-check">
                            <input class="form-check-input"
                                   type="radio"
                                   name="flexRadioDefault"
                                   x-model="paymentMethod"
                                   value="cb"
                                   id="payment_method_cb">
                            <label class="form-check-label" for="payment_method_cb">
                                CB
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="radio"
                                   name="flexRadioDefault"
                                   x-model="paymentMethod"
                                   value="bank_transfer"
                                   id="payment_method_transfer"
                                   checked>
                            <label class="form-check-label" for="payment_method_transfer">
                                Virement
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="radio"
                                   name="flexRadioDefault"
                                   x-model="paymentMethod"
                                   value="check"
                                   id="payment_method_check">
                            <label class="form-check-label" for="payment_method_check">
                                Chèque
                            </label>
                        </div>
                        <div class="row mt-3">
                            <p x-show="paymentMethod === 'check'">
                                A l'ordre de : <strong>Association des amis de la
                                    nature</strong><br>
                            </p>
                            <p x-show="paymentMethod === 'bank_transfer'">
                                IBAN
                            </p>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   value=""
                                   id="flexCheckDefault">
                            <label class="form-check-label" for="flexCheckDefault">
                                J'accepte les conditions d'annulation et de remboursement.

                                <a class="small"
                                   data-bs-toggle="modal"
                                   data-bs-target="#modal-cgv"
                                   href="#">Voir plus</a>
                            </label>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-5">
                            <button @click="page='cart'" class="btn btn-secondary btn-sm">Revenir au panier</button>
                            <button class="btn btn-primary btn-sm">Terminer</button>
                        </div>

                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                <div class="card card-body p-4 shadow">
                    <h4 class="mb-3">Total du panier</h4>
                    <ul class="list-group list-group-borderless mb-2">
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="h6 fw-light mb-0">Hébergement</span>
                            <span class="h6 fw-light mb-0 fw-bold">760€</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="h6 fw-light mb-0">Prestations</span>
                            <span class="h6 fw-light mb-0 fw-bold">400€</span>
                        </li>
                        <hr>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="h5 mb-0">Total à payer</span>
                            <span class="h5 mb-0">1160€</span>
                        </li>
                    </ul>
                    <hr>
                    <h6 class="mb-1">Détails</h6>

                    <ul class="list-group list-group-borderless mb-2">
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="h6 fw-light mb-0 small">Total HT</span>
                            <span class="h6 fw-light mb-0 fw-bold small">xx€</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="h6 fw-light mb-0 small">Montant TVA</span>
                            <span class="h6 fw-light mb-0 fw-bold small">xx€</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="h6 mb-0">Total TTC</span>
                            <span class="h6 mb-0">1160€</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    <x-use-lightbox />
    @include('front.user.cart.cgv-modal')
</x-front-logged-in-layout>
