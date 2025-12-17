<li class="nav-item active"><a class="nav-link" href="{{route('/')}}"><i class="fa fa-home"></i> <span class="toggle-none">Dashboard</span></a></li>
<li class="nav-item">
   <a class="nav-link"  href="javascript: void(0);" aria-expanded="false"><i class="fa fa-th-large"></i> <span class="toggle-none">Mainternance <span class="fa arrow"></span></span></a>
   <ul class="nav-second-level nav flex-column " aria-expanded="false">
      <li class="nav-item"><a class="nav-link" href="{{route('customer')}}">Customer</a></li>
      @if(Auth::user()->role_id=='2')
      	<li class="nav-item"><a class="nav-link" href="widgets-data.html">Spinning</a></li>
      @endif
   </ul>
</li>
@if(Auth::user()->role_id=='2')
<li class="nav-item">
   <a  class="nav-link" href="javascript: void(0);" aria-expanded="false"><i class="fa fa-cogs"></i> <span class="toggle-none">Winner Report</span></a>
</li>
@endif
<li class="nav-item">
   <a class="nav-link" href="javascript: void(0);" aria-expanded="false"><i class="fa fa-file"></i> <span class="toggle-none">Audit Log </span></a>                            
</li>