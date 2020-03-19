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
        $subject = $request->input('subjectRegions');
        $sortedJson = [];

        if ($request->input('send')) {
            $to = implode(',', $json['koordinatori']);
            $headers = 'MIME-Version: 1.0' . '\r\n' . 'Content-type: text/html; charset=UTF-8' . '\r\n' . 'Bcc: distribuce@mzcr.cz';

            $message = '
            <p>Vážení,</p>
            <p>z rozhodnutí Vlády ČR budou pro KRAJ dne XX. XX. 2020 rozvezeny ochranné pomůcky, dle níže uvedeného rozpisu:</p>

                <table class="table table-striped">
                        <thead>
                            <th scope="col">Příjemce</th>
                            <th scope="col">Položka</th>
                            <th scope="col">Množství</th>
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

            <p>Přímořízené organizace státu Vás budou kontaktovat stran vyzvednutí materiálu z odběrového místa. <br>
            Hodnoty jsou přibližné a mohou se lišit dle konkrétní velikosti balení. Prosím o potvrzení doručení zásilky a zaslání scanu dodacího listu na adresu <a href:"mailto:distribuce@mzcr.cz">distribuce@mzcr.cz</a>.</p>

            <p>Moc děkuji za spolupráci.<br>
            S pozdravem<br>
            Distribuční tým OOP MZ ČR</p>
            ';

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
        $subject = $request->input('subjectOrganization');
        $json = json_decode($request->input('json'), true);


        if ($request->input('send')) {
            $to = array_keys($json)[$step];
            $headers = 'MIME-Version: 1.0' . '\r\n' . 'Content-type: text/html; charset=UTF-8' . '\r\n' . 'Bcc: distribuce@mzcr.cz';

            $message = '
            <p>Vážení,</p>
            <p>z rozhodnutí Vlády ČR budou pro KRAJ dne XX. XX. 2020 rozvezeny ochranné pomůcky. Počet OOP pro Vaší organizaci nejdete v níže uvedeném rozpisu:</p>

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

            <p>Stran vyzvednutí zásilky prosím kontaktujte odběrové místo Vašeho kraje: JMÉNO, ČÍSLO, MAIL. Prosím o potvrzení doručení zásilky a zaslání scanu dodacího listu na adresu <a href:"mailto:distribuce@mzcr.cz">distribuce@mzcr.cz</a>.<br>
            Krajský koordinátor a odběrové místo byli o alokaci OOP pro Vaši instituci informováni.</p>

            <p>Moc děkuji za spolupráci.<br>
            S pozdravem<br>
            Distribuční tým OOP MZ ČR</p>
            ';
            mail($to, $subject, $message, $headers);
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
