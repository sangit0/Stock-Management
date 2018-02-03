<table  id="{{$tableID}}" class="table table-hover">
    <thead>
    @if(isset($head))
        <tr>
            {{$head}}
        </tr>
      @endif
    </thead>
    <tbody>
    @if(isset($body))
            {{$body}}
     @endif

    </tbody>

</table>