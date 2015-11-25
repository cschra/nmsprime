
    <!-- begin #sidebar -->
    <div id="sidebar" class="sidebar">
      <!-- begin sidebar scrollbar -->
      <div data-scrollbar="true" data-height="100%">
        <!-- begin sidebar user -->
        <ul class="nav">
          <li class="nav-profile">
            <div class="image">
              <a href="javascript:;"><img src="assets/img/user-11.jpg" alt="" /></a>
            </div>
            <div class="info">
              Sean Ngu
              <small>Front end developer</small>
            </div>
          </li>
        </ul>
        <!-- end sidebar user -->
        <!-- begin sidebar nav -->
        <ul class="nav">

          <li class="nav-header">Navigation</li>
          <li>
            <a href="index.html"><i class="fa fa-laptop"></i> <span>Dashboard</span></a>
          </li>

          <li>
            <a href="inbox.html">
              <span class="badge pull-right">10</span>
              <i class="fa fa-inbox"></i> <span>Inbox</span>
            </a>
          </li>

          @foreach ($networks as $network)
	          <li class="has-sub">
	            <a href="javascript:;">
	              <i class="fa fa-suitcase"></i> 
	              <b class="caret pull-right"></b>
	              <span>{{$network->name}}</span> 
	            </a>
	            <ul class="sub-menu">
	            	<li><a href="/lara/Tree/erd/net/{{$network->id}}">Network</a></li>
		            @foreach ($network->get_all_cluster_to_net() as $cluster)
			           <li><a href="/lara/Tree/erd/cluster/{{$cluster->id}}">--{{$cluster->name}}</a></li>
			        @endforeach
		        </ul>
	          </li>
	       @endforeach


         
          <!-- begin sidebar minify button -->
          <li><a href="javascript:;" class="sidebar-minify-btn" data-click="sidebar-minify"><i class="fa fa-angle-double-left"></i></a></li>
          <!-- end sidebar minify button -->
        </ul>
        <!-- end sidebar nav -->
      </div>
      <!-- end sidebar scrollbar -->
    </div>
    <div class="sidebar-bg"></div>
    <!-- end #sidebar -->



@if(isset($panel_right))
    <!-- begin theme-panel -->
    <div class="theme-panel">
      <a href="javascript:;" data-click="theme-panel-expand" class="theme-collapse-btn">
        <i class="fa fa-cog"></i>
      </a>
      <div class="theme-panel-content">
        <h5 class="m-t-0">Menu</h5>

        <h4>
          @foreach ($panel_right as $menu)
            <?php
              $route = $menu['route'];
              $name  = $menu['name'];
              $link  = $menu['link'];
            ?>
            <br>{{ HTML::linkRoute($route, $name, $link); }}
          @endforeach
        </h4>

      </div>
    </div>
    <!-- end theme-panel -->
@endif