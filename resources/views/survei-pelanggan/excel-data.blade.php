<table>
    <tr>
        <th colspan="{{ count($headers) + 1 }}" style="font-size:14pt;font-weight:bold;text-align:center;">
            DATA MENTAH SURVEI KEPUASAN PELANGGAN
        </th>
    </tr>
    <tr>
        <th colspan="{{ count($headers) + 1 }}" style="font-size:12pt;font-weight:bold;text-align:center;">
            LABORATORIUM MEDIS DAN KLINIK SIMA CILIWUNG MALANG
        </th>
    </tr>
    @if($search)
    <tr>
        <th colspan="{{ count($headers) + 1 }}" style="font-size:10pt;font-style:italic;text-align:center;">
            Filter Pencarian: "{{ $search }}" (Total: {{ count($rows) }} data)
        </th>
    </tr>
    @else
    <tr>
        <th colspan="{{ count($headers) + 1 }}" style="font-size:10pt;font-style:italic;text-align:center;">
            Total Data: {{ count($rows) }} baris
        </th>
    </tr>
    @endif
    <tr>
        <th colspan="{{ count($headers) + 1 }}"></th>
    </tr>
    <thead>
        <tr>
            <th style="font-weight:bold;text-align:center;background-color:#d9d9d9;border:1px solid #000000;width:50px;">NO</th>
            @foreach($headers as $h)
            <th style="font-weight:bold;text-align:center;background-color:#d9d9d9;border:1px solid #000000;">
                {{ $h }}
            </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $idx => $r)
        <tr>
            <td style="text-align:center;border:1px solid #000000;">{{ $idx + 1 }}</td>
            @foreach($headers as $cIdx => $h)
            <td style="border:1px solid #000000;">{{ $r[$cIdx] ?? '' }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>

