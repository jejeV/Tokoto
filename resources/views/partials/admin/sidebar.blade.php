<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
      <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
        <span class="app-brand-text demo menu-text fw-bolder ms-2">shoesbaru</span>
      </a>

      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
        <i class="bx bx-chevron-left bx-sm align-middle"></i>
      </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
      <!-- Dashboard -->
      <li class="menu-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
        <a href="{{ route('admin.dashboard') }}" class="menu-link">
          <i class="menu-icon tf-icons bx bx-home-circle"></i>
          <div>Dashboard</div>
        </a>
      </li>

      <!-- Produk -->
      <li class="menu-item {{ request()->is('admin/products*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons bx bx-box"></i>
          <div>Produk</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item {{ request()->is('admin/products') ? 'active' : '' }}">
            <a href="" class="menu-link">
              <div>Daftar Produk</div>
            </a>
          </li>
          <li class="menu-item {{ request()->is('admin/products/create') ? 'active' : '' }}">
            <a href="" class="menu-link">
              <div>Tambah Produk</div>
            </a>
          </li>
          <li class="menu-item {{ request()->is('admin/products/categories') ? 'active' : '' }}">
            <a href="" class="menu-link">
              <div>Kategori</div>
            </a>
          </li>
        </ul>
      </li>

      <!-- Pesanan -->
      <li class="menu-item {{ request()->is('admin/orders*') ? 'active' : '' }}">
        <a href="" class="menu-link">
          <i class="menu-icon tf-icons bx bx-cart"></i>
          <div>Pesanan</div>
        </a>
      </li>

      <!-- Pelanggan -->
      <li class="menu-item {{ request()->is('admin/customers*') ? 'active' : '' }}">
        <a href="" class="menu-link">
          <i class="menu-icon tf-icons bx bx-user"></i>
          <div>Pelanggan</div>
        </a>
      </li>

      <!-- Laporan -->
      <li class="menu-item {{ request()->is('admin/reports*') ? 'active' : '' }}">
        <a href="" class="menu-link">
          <i class="menu-icon tf-icons bx bx-bar-chart"></i>
          <div>Laporan</div>
        </a>
      </li>
    </ul>
  </aside>
