<table>
    <tr>
        <td colspan="{{ count((array)$data->first()) + 2 }}" 
            style="font-weight:bold; font-size:18px; text-align:center;">
            EMBASE INSTITUTION
        </td>
    </tr>

    <tr></tr>

    <tr>
        <td colspan="{{ count((array)$data->first()) + 2 }}" 
            style="font-weight:bold; text-align:center;">
             {{ $paper_name ?? '-' }} assessment report
        </td>
    </tr>

    <tr></tr>

    @php
        $firstRow = $data->first();
        $assessmentColumns = collect((array)$firstRow)
                                ->except(['full_name', 'admission_number', 'total'])
                                ->keys();
        $si = 1;
    @endphp

    <tr>
        <th style="width:50px; text-align:center;">Si. No</th>
        <th style="width:150px; text-align:center;">Admission Number</th>
        <th style="width:200px; text-align:center;">Student Name</th>
        @foreach ($assessmentColumns as $col)
            <th style="width:100px; text-align:center;">{{ ucfirst($col) }}</th>
        @endforeach
        <th style="width:100px; text-align:center;">Total</th>
    </tr>

    @foreach ($data as $row)
        <tr>
            <td style="text-align:center;">{{ $si++ }}</td>
            <td style="text-align:center;">{{ $row->admission_number }}</td>
            <td style="text-align:center;">{{ $row->full_name }}</td>
            @foreach ($assessmentColumns as $col)
                <td style="text-align:center;">{{ $row->$col ?? '-' }}</td>
            @endforeach
            <td style="text-align:center;">{{ $row->total ?? '-' }}</td>
        </tr>
    @endforeach
</table>
