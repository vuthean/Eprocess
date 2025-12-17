<nav class="pcoded-navbar">
    <div class="pcoded-inner-navbar main-menu">
        <div class="pcoded-navigatio-lavel">Navigation</div>
        <ul class="pcoded-item pcoded-left-item">
            <li class="">
                <a href="{{ route('/') }}" id="dashboard">
                    <span class="pcoded-micon"><i class="feather icon-home"></i></span>
                    <span class="pcoded-mtext">Dashboard</span>
                </a>
            </li>
            @if (Session::get('is_admin') == '1')
                <li class="pcoded-hasmenu active pcoded-trigger">
                    <a href="javascript:void(0)">
                        <span class="pcoded-micon"><i class="feather icon-users"></i></span>
                        <span class="pcoded-mtext">Administrator</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class=" ">
                            <a href="{{ route('group/listing') }}">
                                <span class="pcoded-mtext">Group/User Mgt</span>
                            </a>
                        </li>
                        <li class=" ">
                            <a href="{{ route('specialgroup/listing') }}">
                                <span class="pcoded-mtext">Special Group</span>
                            </a>
                        </li>
                        <li class=" ">
                            <a href="{{ route('branchcode/listing') }}">
                                <span class="pcoded-mtext">Branch Code</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="{{ route('budgetcode/listing') }}">
                                <span class="pcoded-mtext">Budget Code</span>
                            </a>
                        </li>
                        <li class=" ">
                            <a href="{{ route('user_log.index') }}">
                                <span class="pcoded-mtext">User tracking</span>
                            </a>
                        </li>
                        <li  class="">
                            <a href="{{ route('block_form') }}">
                                <span class="pcoded-mtext">Block Form</span>
                            </a>
                        </li>
                        @if(Session::get('is_accounting_team'))
                        <li  class=" active">
                            <a href="{{ route('listing/users') }}">
                                <span class="pcoded-mtext">List Users</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
            @endif
            <li class="pcoded-hasmenu">
                <a href="javascript:void(0)">
                    <span class="pcoded-micon"><i class="feather icon-edit-1"></i></span>
                    <span class="pcoded-mtext">Form Request</span>
                </a>
                <ul class="pcoded-submenu">
                    <li class="">
                        <a href="{{ route('procurement_request.list') }}">
                            <span class="pcoded-mtext">Procurement</span>
                        </a>
                    </li>
                    <li class=" ">
                        <a href="{{ route('payment_request.list_auth') }}">
                            <span class="pcoded-mtext">Payment</span>
                        </a>
                    </li>
                    <li class=" ">
                        <a href="{{ route('form/advances') }}">
                            <span class="pcoded-mtext">Advance</span>
                        </a>
                    </li>
                    <li class=" ">
                        <a href="{{ route('form/clear-advances') }}">
                            <span class="pcoded-mtext">Clear Advance</span>
                        </a>
                    </li>
                    @if(Session::get('is_treasury') == '1' or Session::get('is_accounting_team') == '1' or Session::get('is_finance_team') == '1')
                        <li class=" ">
                            <a href="{{ route('form/bank-payment-vouchers') }}">
                                <span class="pcoded-mtext">Bank Payment Voucher</span>
                            </a>
                        </li>
                        <li class=" ">
                            <a href="{{ route('form/journal-vouchers') }}">
                                <span class="pcoded-mtext">Journal Voucher</span>
                            </a>
                        </li>
                        <li class=" ">
                            <a href="{{ route('form/bank-receipt-vouchers') }}">
                                <span class="pcoded-mtext">Bank Receipt Voucher</span>
                            </a>
                        </li>
                        <li class=" ">
                            <a href="{{ route('form/cash-payment-vouchers') }}">
                                <span class="pcoded-mtext">Cash Payment Voucher</span>
                            </a>
                        </li>
                        <li class=" ">
                            <a href="{{ route('form/cash-receipt-vouchers') }}">
                                <span class="pcoded-mtext">Cash Receipt Voucher</span>
                            </a>
                        </li>
                    @endif
                    <li class=" " hidden="">
                        <a href="icon-flags.html">
                            <span class="pcoded-mtext">Clear / Anvanced Clear</span>
                        </a>
                    </li>
                    <li class=" " hidden="">
                        <a href="icon-flags.html">
                            <span class="pcoded-mtext">Accounting Voucher</span>
                        </a>
                    </li>
                    <li class=" ">
                        <a href="{{ route('form/cash-payment-vouchers') }}">
                            <span class="pcoded-mtext">Cash Payment Voucher</span>
                        </a>
                    </li>
                    @if (Session::get('is_treasury') == '1')
                        <li class=" ">
                            <a href="{{ route('form/bank-vouchers') }}">
                                <span class="pcoded-mtext">Treasury Voucher</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
            @if (Session::get('is_admin') == '1')
                <li class="pcoded-hasmenu">
                    <a href="javascript:void(0)">
                        <span class="pcoded-micon"><i class="feather icon-edit-1"></i></span>
                        <span class="pcoded-mtext">Reports</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class=" ">
                            <a href="{{ route('reports/payment-tracking-request') }}">
                                <span class="pcoded-mtext">Payment Tracking</span>
                            </a>
                        </li>
                        @if(Session::get('is_treasury') == '1' or Session::get('is_accounting_team') == '1' or Session::get('is_finance_team') == '1')
                            <li class="{{ request()->is('reports/payment-tracking-request') || request()->is('reports/filter-payment-tracking-request') || request()->is('reports/advance-tracking-request') || request()->is('reports/filter-advance_tracking-request') || request()->is('reports/clear-advance-tracking-request') || request()->is('reports/filter-clear-advance-tracking-request')  ? 'pcoded-hasmenu active  pcoded-trigger' : 'pcoded-hasmenu' }}">
                                <a href="javascript:void(0)">
                                    <span class="pcoded-micon"><i class="feather icon-edit-1"></i></span>
                                    <span class="pcoded-mtext">Accounting Voucher</span>
                                </a>
                                <ul class="pcoded-submenu">
                                    <li class=" ">
                                        <a href="{{ route('reports/accounting-voucher-tracking-request-search') }}">
                                            <span class="pcoded-mtext">Bank Payment Voucher</span>
                                        </a>
                                    </li>
                                    <li class=" ">
                                        <a href="{{ route('reports/journal-tracking-request-search') }}">
                                            <span class="pcoded-mtext">Journal Voucher</span>
                                        </a>
                                    </li>
                                    <li class=" ">
                                        <a href="{{ route('reports/bank-receipt-tracking-request-search') }}">
                                            <span class="pcoded-mtext">Bank Receipt Voucher</span>
                                        </a>
                                    </li>
                                    <li class=" ">
                                        <a href="{{ route('reports/cash-payment-tracking-request-search') }}">
                                            <span class="pcoded-mtext">Cash Payment Voucher</span>
                                        </a>
                                    </li>
                                    <li class=" ">
                                        <a href="{{ route('reports/cash-receipt-tracking-request-search') }}">
                                            <span class="pcoded-mtext">Cash Receipt Voucher</span>
                                        </a>
                                    </li>
                                </ul>
                                
                            </li>
                        @endif
                        <li class="{{ request()->is('reports/payment-tracking-request') || request()->is('reports/filter-payment-tracking-request') || request()->is('reports/advance-tracking-request') || request()->is('reports/filter-advance_tracking-request') || request()->is('reports/clear-advance-tracking-request') || request()->is('reports/filter-clear-advance-tracking-request')  ? 'pcoded-hasmenu active  pcoded-trigger' : 'pcoded-hasmenu' }}">
                            <a href="javascript:void(0)">
                                <span class="pcoded-micon"><i class="feather icon-layers"></i></span>
                                <span class="pcoded-mtext">Procurement Tracking</span>
                            </a>
                            <ul class="pcoded-submenu">
                                <li class=" ">
                                    <a href="{{ route('reports/procurement-tracking-request') }}">
                                        <span class="pcoded-mtext"> Procurement Report </span>
                                    </a>
                                </li>
                                <li class=" ">
                                    <a href="{{ route('reports/payment-procurement-tracking-request') }}">
                                        <span class="pcoded-mtext"> RP & PR Report </span>
                                    </a>
                                </li>
                                <li class=" ">
                                    <a href="{{ route('reports/advance-clear-procurement-tracking-request') }}">
                                        <span class="pcoded-mtext">ADC & ADV & PR Report </span>
                                    </a>
                                </li>
                            </ul>
                            
                        </li>
                        <!-- <li class=" ">
                            <a href="{{ route('reports/procurement-tracking-request') }}">
                                <span class="pcoded-mtext">Procurement Tracking</span>
                            </a>
                        </li> -->
                        <li class=" ">
                            <a href="{{ route('reports/budget-tracking-request') }}">
                                <span class="pcoded-mtext">Budget Tracking</span>
                            </a>
                        </li>
                    </ul>
                    <ul class="pcoded-submenu">
                        <li class=" ">
                            <a href="{{ route('reports/advance-tracking-request') }}">
                                <span class="pcoded-mtext">Advance Tracking</span>
                            </a>
                        </li>
                    </ul>
                    <ul class="pcoded-submenu">
                        <li class=" ">
                            <a href="{{ route('reports/clear-advance-tracking-request') }}">
                                <span class="pcoded-mtext">Clear Advance Tracking</span>
                            </a>
                        </li>
                    </ul>

                </li>
            @endif
            @if (Session::get('is_allow_procurement') == '1')
                <li class="">
                    <a href="{{ route('form/procurement/listing') }}">
                        <span class="pcoded-micon"><i class="feather icon-layers"></i></span>
                        <span class="pcoded-mtext">Procurement Record</span>
                    </a>
                </li>
            @endif
            <li class="">
                <a href="{{ route('auditlog/listing') }}">
                    <span class="pcoded-micon"><i class="feather icon-layers"></i></span>
                    <span class="pcoded-mtext">Audit Log</span>
                </a>
            </li>
