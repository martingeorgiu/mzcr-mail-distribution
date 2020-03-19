<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class MainController extends Controller
{
    public function intro()
    {
        return view('intro');
    }

    public function uploadJson(Request $request)
    {
        $this->validate($request, ['json' => 'required']);
        $json = json_decode($request->input('json'), true);
        return view('regions', ['json' => $json]);
    }

    public function sendRegions(Request $request)
    {
        $this->validate($request, [
            'json' => 'required',
            'subjectRegions' => 'required',
        ]);
        $json = json_decode($request->input('json'), true);
        $sortedJson = [];

        if ($request->input('send')) {
            // send email
        }

        foreach ($json['polozky'] as $item) {
            if (!array_key_exists($item['email'], $sortedJson)) {
                $sortedJson[$item['email']] = [
                    'organization' => $item['organizace'],
                    'items' => [],
                ];
            }
            $sortedJson[$item['email']]['items'][] = [
                'item' => $item['polozka'],
                'amount' => $item['mnozstvi'],
            ];
        }


        return view('organizations', [
            'json' => $sortedJson,
            'step' => 0,
            'key' => array_keys($sortedJson)[0],
        ]);
    }

    public function sendOrganization(Request $request)
    {
        $this->validate($request, [
            'json' => 'required',
            'step' => 'required',
            'subjectOrganization' => 'required',
        ]);
        $step = $request->input('step');
        $json = json_decode($request->input('json'), true);


        if ($request->input('send')) {
            // send email
        }

        $step++;
        if ($step >= count($json)) {
            return redirect('/finished');
        }

        return view('organizations', [
            'json' => $json,
            'step' => $step,
            'key' => array_keys($json)[$step],
        ]);
    }
    public function finished()
    {
        return view('finished');
    }
}
