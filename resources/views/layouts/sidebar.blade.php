<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li>
                    <a href="#" class="waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-chat">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('sales.index') }}" class="waves-effect">
                        <i class="bx bxs-chart"></i>
                        <span key="t-chat">Sales</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('products.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('products.index') }}" class="waves-effect">
                        <i class="bx bx-cart"></i>
                        <span key="t-chat">Product</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('customers.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('customers.index') }}" class="waves-effect">
                        <i class="bx bxs-user"></i>
                        <span key="t-chat">Customer</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
