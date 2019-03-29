<ul class="timeline">
    @foreach($logs as $log)
        <li>
            <!-- begin timeline-time -->
            <div class="timeline-time">
                <span class="date">{{$log->updated_at->format('d-m-Y')}}</span>
                <span class="time">{{$log->updated_at->format('H:i:s')}}</span>
            </div>
            <!-- end timeline-time -->
            <!-- begin timeline-icon -->
            <div class="timeline-icon">
                <a href="javascript:;">&nbsp;</a>
            </div>
            <!-- end timeline-icon -->
            <!-- begin timeline-body -->
            <div class="timeline-body">
                <div class="timeline-header">
                    <span class="userimage"><i class="fa fa-user-circle-o fa-lg"></i></span>
                    <span class="username">{{$log->username}}</span>
                    <span class="pull-right text-muted">{{$log->updated_at->diffForHumans()}}</span>
                </div>
                <div class="timeline-content">
                    <p class="lead">
                        <i class="fa fa-pencil-square-o"></i>
                        {{$log->username}} {{$log->method}} {{$log->model}}
                    </p>
                </div>
            </div>
            <!-- end timeline-body -->
        </li>
    @endforeach
</ul>
