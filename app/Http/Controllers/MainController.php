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
        if ($json == null || !array_key_exists('koordinatori', $json) || !array_key_exists('kraj', $json) ||  !array_key_exists('polozky', $json) ||  !array_key_exists('odberne misto', $json) || empty($json['koordinatori']) ||  empty($json['kraj']) || empty($json['polozky']) || empty($json['odberne misto'])) return redirect('/');
        foreach ($json['polozky'] as $item) {
            if ($item == null || !array_key_exists('organizace', $item) || !array_key_exists('polozka', $item) || !array_key_exists('mnozstvi', $item) || !array_key_exists('email', $item) || empty($item['organizace']) || empty($item['polozka']) || empty($item['mnozstvi']) || empty($item['email'])) return redirect('/');
        };
        $_SESSION['rawJson'] = $json;
        return view('regions', [
            'json' => $json,
            'date' => date('j. n. Y'),
        ]);
    }

    public function sendRegions(Request $request)
    {
        $this->validate($request, [
            'subjectRegions' => 'required',
            'topBodyRegions' => 'required',
            'bottomBodyRegions' => 'required',
        ]);
        $rawJson = $_SESSION['rawJson'];
        $subject = $request->input('subjectRegions');
        $topBodyRegions = nl2br($request->input('topBodyRegions'));
        $bottomBodyRegions = nl2br($request->input('bottomBodyRegions'));
        $success = 0;
        $sortedJson = [];

        if ($request->input('send')) {
            $to = $rawJson['odberne misto']['email'];
            $headers =
                "MIME-Version: 1.0\r\n" .
                "Content-Type: text/html; charset=UTF-8\r\n" .
                "Content-Transfer-Encoding: 8bit\r\n" .
                "Bcc: distribuce@mzcr.cz" .
                (!empty($rawJson['koordinatori']) ? ', '.implode(', ', $rawJson['koordinatori']) : '') .
                "\r\n" .
                'From: distribuce@mzcr.cz';

            $message = '<p>' . $topBodyRegions . '</p>
                <table class="table table-striped" cellspacing="0" border="1">
                        <thead>
                            <tr>
                                <th>Příjemce</th>
                                <th>Položka</th>
                                <th>Množství</th>
                                <th>E-mail</th>
                                <th>Telefon</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($rawJson['polozky'] as $item) {
                $message .= '<tr>
                    <td>' . $item['organizace'] . '</td>
                    <td>' . $item['polozka'] . '</td>
                    <td>' . $item['mnozstvi'] . '</td>
                    <td>' . $item['email'] . '</td>
                    <td>' . (isset($item['telefon']) ? $item['telefon'] : '') . '</td>
                </tr>';
            }
            $message .= '</tbody>
                </table>
           <p> ' . $bottomBodyRegions . '</p>';

            mail($to, $subject, $message, $headers, '-f distribuce@mzcr.cz');
            $success = count($rawJson['koordinatori']) + 1;
        }

        foreach ($rawJson['polozky'] as $item) {
            if (!array_key_exists($item['email'], $sortedJson)) {
                $sortedJson[$item['email']] = [
                    'organization' => $item['organizace'],
                    'copy' => isset($item['kopie']) ? $item['kopie'] : '',
                    'tel' => isset($item['telefon']) ? $item['telefon'] : '',
                    'signature' => isset($item['podpis']) ? $item['podpis'] : 'Distribuční tým OOP MZ ČR',
                    'items' => [],
                ];
            }
            $sortedJson[$item['email']]['items'][] = [
                'item' => $item['polozka'],
                'amount' => $item['mnozstvi'],
            ];
        }
        $_SESSION['sortedJson'] = $sortedJson;
        return view('organizations', [
            'json' => $sortedJson,
            'step' => 0,
            'region' => $rawJson['kraj'],
            'om' => $rawJson['odberne misto'],
            'key' => array_keys($sortedJson)[0],
            'date' => date('j. n. Y'),
            'success' =>  $success,
        ]);
    }

    public function sendOrganization(Request $request)
    {
        $this->validate($request, [
            'step' => 'required',
            'subjectOrganization' => 'required',
            'topBodyRegions' => 'required',
            'bottomBodyRegions' => 'required',
        ]);
        $sortedJson = $_SESSION['sortedJson'];
        $rawJson = $_SESSION['rawJson'];
        $step = $request->input('step');
        $subject = $request->input('subjectOrganization');
        $topBodyRegions = nl2br($request->input('topBodyRegions'));
        $bottomBodyRegions = nl2br($request->input('bottomBodyRegions'));
        $success = 0;

        if ($request->input('send')) {
            $to = array_keys($sortedJson)[$step];

            //echo '<pre>';
            //print_r($step);
            //print_r($sortedJson);

            $headers =
                "MIME-Version: 1.0\r\n" .
                "Content-Type: text/html; charset=UTF-8\r\n" .
                "Content-Transfer-Encoding: 8bit\r\n" .
                // TODO: Tento radek nize je spatne. Pole $sortedJson[$step] neobsahuje data. Promenna $step je ciselna, ale 
                // $sortedJson neobsahuje ciselne indexy
                //(empty($sortedJson[$step]['copy']) ? '' : "Cc: " . $sortedJson[$step]['copy'] . "\r\n") .
                "Bcc: distribuce@mzcr.cz" .
                (!empty($rawJson['odberne misto']['email']) ? ', '.$rawJson['odberne misto']['email'] : '') .
                "\r\n" .
                'From: distribuce@mzcr.cz';

            //print_r($headers);
            //echo '</pre>';exit;

            $message = '<p>' . $topBodyRegions . '</p>
                <table class="table table-striped" cellspacing="0" border="1">
                        <thead>
                            <tr>
                                <th scope="col">Položka</th>
                                <th scope="col">Množství</th>
                            </tr>
                        </thead>
                        <tbody>';
            foreach ($sortedJson[array_keys($sortedJson)[$step]]['items'] as $item) {
                $message .= '<tr>
                    <td>' . $item['item'] . '</td>
                    <td>' . $item['amount'] . '</td>
                </tr>';
            }
            $message .= '</tbody>
                </table>
            <p>' . $bottomBodyRegions . '</p>';
            mail($to, $subject, $message, $headers, '-f distribuce@mzcr.cz');
            $success = 1;
        }

        $step++;
        if ($step >= count($sortedJson)) {
            return redirect('/finished?success=' . $success);
        }

        return view('organizations', [
            'json' => $sortedJson,
            'step' => $step,
            'region' => $rawJson['kraj'],
            'om' => $rawJson['odberne misto'],
            'key' => array_keys($sortedJson)[$step],
            'date' => date('j. n. Y'),
            'success' =>  $success,
        ]);
    }
    public function finished(Request $request)
    {
        return view('finished', [
            'success' => $request->input('success'),
        ]);
    }
}
