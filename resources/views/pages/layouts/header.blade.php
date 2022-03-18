<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ route("pages.products.index") }}">SHOP</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-header-collapse" aria-controls="main-header-collapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="main-header-collapse">
            <div class="navbar-nav w-100">
                <a class="nav-item nav-link" href="{{ route("pages.products.index") }}">Products</a>
                <a class="nav-item nav-link ml-lg-auto" href="{{ route("pages.carts.index") }}"><i class="fa fa-shopping-cart"></i></a>
            </div>
        </div>
    </div>
</nav>