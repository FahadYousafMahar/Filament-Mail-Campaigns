<?php

namespace App\Livewire;

use Livewire\Component;

class EmailTemplatePreview extends Component
{
    public $template = '';
    public $variables = [
        'username' => 'John Doe',
        'password' => '123456',
        'subscription_type' => 'New',
        'duration' => '1 month',
        'client_full_name' => 'John Doe',
        'client_email' => 'client@example.com',
    ];
    public function render()
    {
        $preview = str_replace(array_map(fn($var) => '{{' . $var . '}}', array_keys($this->variables)), array_values($this->variables), $this->template);
        return view('livewire.email-template-preview', ['preview' => $preview]);
    }
}
