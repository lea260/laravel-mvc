<?php

use App\Models\Auto;

// Route::get('/', function () {
//     return view('welcome');
// });

// routes/web.php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $autos = Auto::all();
    return Auto::all();
});
