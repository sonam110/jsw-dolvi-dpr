
@if($type=='log')
<div class="card">
    <div class="card-body">
      <div class="table-responsive text-nowrap">
        <table class="table table-bordered">
          <thead class="table-light">
           <tr>
            <th class="blue" width="15%">
              SNO
           </th>
            <th class="blue" width="15%">
              DATE
           </th>

            <th class="blue" width="15%">
              ACTION
           </th>

          </tr>
          </thead>
          <tbody>
            @foreach($data as $key => $row)
            <tr>
              <td>{{ $key+1 }}</td>
              <td>{{ $row['date'] }}</td>
              @if($row['link']=='')
              <td style="color:red"> 
                {{ $row['name'] }}
                </td>
              @else
              <td> <a href="{{ $row['link']  }}" download>
                 {{ $row['name'] }}
                
              </a></td>
              @endif
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
</div>

@endif
@if($type=='')
<div class="card">
    <div class="card-body">
        <div class="row">
            @foreach($data as $key => $row)
            <div class="col-md-2">
                <div class="card mb-3" >
                    <div class="card-body card-inside">
                        <a href="{{ $row['link'] }}" download><img src="{{ url('/excel-dad2c1ae.png') }}" alt="Image" width="80px"></a>
                        <p><a href="{{ $row['link'] }}" download>{{ $row['name']}}</a></p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

 
@endif