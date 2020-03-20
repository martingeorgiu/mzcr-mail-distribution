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
        if ($json == null || !array_key_exists('koordinatori', $json) || !array_key_exists('kraj', $json) ||  !array_key_exists('polozky', $json) || empty($json['koordinatori']) ||  empty($json['kraj']) || empty($json['polozky'])) return redirect('/');
        foreach ($json['polozky'] as $item) {
            if ($item == null || !array_key_exists('organizace', $item) || !array_key_exists('polozka', $item) || !array_key_exists('mnozstvi', $item) || !array_key_exists('email', $item) || empty($item['organizace']) || empty($item['polozka']) || empty($item['mnozstvi']) || empty($item['email'])) return redirect('/');
        };
        return view('regions', [
            'json' => $json,
            'date' => date('j. n. Y'),
        ]);
    }

    public function sendRegions(Request $request)
    {
        $this->validate($request, [
            'json' => 'required',
            'subjectRegions' => 'required',
            'topBodyRegions' => 'required',
            'bottomBodyRegions' => 'required',

        ]);
        $json = json_decode($request->input('json'), true);
        $subject = $request->input('subjectRegions');
        $topBodyRegions = nl2br($request->input('topBodyRegions'));
        $bottomBodyRegions = nl2br($request->input('bottomBodyRegions'));

        $sortedJson = [];

        if ($request->input('send')) {
            $to = implode(',', $json['koordinatori']);
            $headers = 'MIME-Version: 1.0' . '\r\n' . 'Content-type: text/html; charset=UTF-8' . '\r\n' . 'Bcc: distribuce@mzcr.cz';

            $message = '<p>' . $topBodyRegions . '</p>
                <table>
                        <thead>
                            <th>Příjemce</th>
                            <th>Položka</th>
                            <th>Množství</th>
                        </thead>
                        <tbody>';
            foreach ($json['polozky'] as $item) {
                $message .= '<tr>
					<td>' . $item['organizace'] . '</td>
					<td>' . $item['polozka'] . '</td>
					<td>' . $item['mnozstvi'] . '</td>
				</tr>';
            }
            $message .= '</tbody>
                </table>
           <p> ' . $bottomBodyRegions . '</p>';

            mail($to, $subject, $message, $headers);
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
            'region' => $json['kraj'],
            'key' => array_keys($sortedJson)[0],
            'date' => date('j. n. Y'),
        ]);
    }

    public function sendOrganization(Request $request)
    {
        $this->validate($request, [
            'json' => 'required',
            'step' => 'required',
            'region' => 'required',
            'subjectOrganization' => 'required',
            'topBodyRegions' => 'required',
            'bottomBodyRegions' => 'required',
        ]);
        $json = json_decode($request->input('json'), true);
        $step = $request->input('step');
        $region = $request->input('region');
        $subject = $request->input('subjectOrganization');
        $topBodyRegions = nl2br($request->input('topBodyRegions'));
        $bottomBodyRegions = nl2br($request->input('bottomBodyRegions'));



        if ($request->input('send')) {
            $to = array_keys($json)[$step];
            $headers = 'MIME-Version: 1.0' . '\r\n' . 'Content-type: text/html; charset=UTF-8' . '\r\n' . 'Bcc: distribuce@mzcr.cz';

            $message = '<p>' . $topBodyRegions . '</p>
                <table class="table table-striped">
                        <thead>
                            <th scope="col">Položka</th>
                            <th scope="col">Množství</th>
                        </thead>
                        <tbody>';
            foreach ($json[array_keys($json)[$step]]['items'] as $item) {
                $message .= '<tr>
					<td>' . $item['item'] . '</td>
					<td>' . $item['amount'] . '</td>
				</tr>';
            }
            $message .= '</tbody>
                </table>
            <p>' . $bottomBodyRegions . '</p>';
            mail($to, $subject, $message, $headers);
        }

        $step++;
        if ($step >= count($json)) {
            return redirect('/finished');
        }

        return view('organizations', [
            'json' => $json,
            'step' => $step,
            'region' => $region,
            'key' => array_keys($json)[$step],
            'date' => date('j. n. Y'),
        ]);
    }
    public function finished()
    {
        return view('finished');
    }
}
