<?php

namespace App\Livewire;

use Livewire\Component;

class Contactform extends Component
{


    public $name = '';
    public $email = '';
    public $message = '';
    public $number = '';

    public function submit()
    {
        $validated = $this->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
            'number' => 'required',
        ]);

        dd($validated);
    }
    public function render()
    {
        return view('livewire.contactform');
    }
}
