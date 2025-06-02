<?php

namespace App\MailTemplates\Traits;

use MetaFramework\Traits\Responses;
use Throwable;

/**
 * @property \App\MailTemplates\Models\MailTemplate
 */

trait MailTemplate
{
    use Responses;

    public string $subject = '';
    public string $content = '';
    public array $attachment = [];

    public ?array $variables_data = NULL;



    public function computed(): array
    {
        if(is_array($this->variables_data)){
            return $this->variables_data;
        }

        foreach (array_keys($this->variables()) as $item) {
            $this->variables_data['{' . $item . '}'] = method_exists($this, $item) ? $this->{$item}() : '<span style="color:red">'.$item.'</span>';
        }
        return $this->variables_data;
    }

    public function canServe(): bool
    {
        return $this->template instanceof \App\MailTemplates\Models\MailTemplate;
    }

    public function serve(): static
    {
        if ($this->canServe()) {
            $this->subject = strip_tags(str_replace(array_keys($this->computed()), array_values($this->computed()), $this->template->subject));
            $this->content = str_replace(array_keys($this->computed()), array_values($this->computed()), $this->template->content);
        } else {
            $this->responseWarning("Template " . $this->signature() . " can't serve content.");
        }
        return $this;
    }

    public function content(): array
    {
        return [
          'subject' => $this->subject,
          'content' => $this->content
        ];
    }
}
