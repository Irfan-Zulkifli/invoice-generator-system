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
                    <a href="#" class="waves-effect">
                        <i class="fas fa-file-invoice"></i>
                        <span key="t-chat">Invoice</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('products.index') }}" class="waves-effect">
                        <i class="fas fa-shopping-basket"></i>
                        <span key="t-chat">Product</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('customers.index') }}" class="waves-effect">
                        <i class="fas fa-users"></i>
                        <span key="t-chat">Customer</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
