<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class Sidebar extends Component
{
    public $proyeks;

    public function __construct()
    {
        $this->proyeks = Auth::check()
            ? Auth::user()
            ->proyeks()
            ->select('proyek.*')
            ->orderBy('proyek.nama_proyek', 'asc')
            ->get()
            : collect();
    }

    public function render()
    {
        return view('components.sidebar');
    }
}
