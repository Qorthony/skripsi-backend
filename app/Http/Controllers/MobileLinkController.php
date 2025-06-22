<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class MobileLinkController extends Controller
{
    public function index(Request $request)
    {
        $variant = $request->query('variant');
        $env = $variant?? 'preview';
        return Inertia::render('MobileLink',[
            'intentLink'=>'intent://#Intent;scheme=skripsi;package=com.qorthony.skripsi.'.$env.';end',
            'downloadLink'=>'https://expo.dev/accounts/qorthony/projects/skripsi/builds/77d96a6b-a71e-4786-afc2-0e3ddf67aa14'
        ]);
    }

    public function event(Request $request, $event)
    {
        $variant = $request->query('variant');
        $env = $variant?? 'preview';
        return Inertia::render('MobileLink',[
            'intentLink'=>'intent://event/'.$event.'#Intent;scheme=skripsi;package=com.qorthony.skripsi.'.$env.';end',
            'downloadLink'=>'https://expo.dev/accounts/qorthony/projects/skripsi/builds/77d96a6b-a71e-4786-afc2-0e3ddf67aa14'
        ]);
    }

    public function gatekeeper(Request $request, $kode_akses)
    {
        $variant = $request->query('variant');
        $env = $variant?? 'preview';
        return Inertia::render('MobileLink',[
            'intentLink'=>'intent://gatekeeper/'.$kode_akses.'#Intent;scheme=skripsiorganizer;package=com.qorthony.skripsiorganizer.'.$env.';end',
            'downloadLink'=>'https://expo.dev/accounts/qorthony/projects/skripsi-organizer/builds/582159da-4861-404e-96d6-3df400a085be'
        ]);
    }
}
