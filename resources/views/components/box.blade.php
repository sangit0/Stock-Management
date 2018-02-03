    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">{{$title}}</h3>
            @if(isset($tool))
            {{$tool}}
            @endif

        </div>
     @if(isset($body))
        <div class="box-body">
            {{$body}}
        </div>
     @endif

    @if(isset($footer))
            <div class="box-footer">
                {{$footer}}
            </div>
         @endif

    </div>
