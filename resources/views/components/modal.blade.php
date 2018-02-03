<div class="modal fade" id="{{$ID}}">
    <div class="modal-dialog {{$type or ""}}">
        <div class="modal-content">
            @if(isset($title))
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{$title}}</h4>
            </div>
            @endif
            <div class="modal-body">
                    {{$body}}
            </div>
            @if(isset($footer))
            <div class="modal-footer">
                {{$footer}}
            </div>
            @endif
        </div>
    </div>
</div>