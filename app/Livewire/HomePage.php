<?php

namespace App\Livewire;
use App\Models\Categories;
use Livewire\Attributes\Title;
use Livewire\Component;

class HomePage extends Component
{
    #[Title('Home Page')] 
    public function render()
    {

        $category = Categories::where('is_active', true)->get();

        return view('livewire.home-page', [
            'category' => $category
        ]);
    }
}
