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

    // Flag to track if highlighting is enabled
    private bool $highlightVariables = false;



    public function computed(): array
    {
        if(is_array($this->variables_data)){
            return $this->variables_data;
        }

        foreach (array_keys($this->variables()) as $item) {
            if (method_exists($this, $item)) {
                $value = $this->{$item}();

                // Only apply special styling if highlighting is enabled
                if ($this->highlightVariables) {
                    // Check if value is empty
                    if (empty($value) && $value !== '0' && $value !== 0) {
                        $value = '<span style="color:red;">vide</span>';
                    } else {
                        // Apply highlighting for non-empty values - blue
                        $value = '<span style="color: blue;">' . $value . '</span>';
                    }
                }
            } else {
                // Method doesn't exist
                $value = $this->highlightVariables
                    ? '<span style="color:black; text-decoration: line-through;">Method missing</span>'
                    : '<span style="color:red">'.$item.'</span>';
            }

            $this->variables_data['{' . $item . '}'] = $value;
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

    /**
     * Enable highlighting of variable values in red
     *
     * @return static
     */
    public function highlight(): static
    {
        $this->highlightVariables = true;
        // Reset computed data to force recomputation with highlighting
        $this->variables_data = null;
        return $this;
    }

    /**
     * Disable highlighting of variable values (default behavior)
     *
     * @return static
     */
    public function unhighlight(): static
    {
        $this->highlightVariables = false;
        // Reset computed data to force recomputation without highlighting
        $this->variables_data = null;
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
