@if(Request::segment(1) == "super-admin" )
    <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#programmerLayouts" aria-expanded="true" aria-controls="programmerLayouts">
        <div class="sb-nav-link-icon"><i class="fas fa-file-lines"></i></div>
        Super Admin
        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
    </a>
    <div class="collapse show" id="programmerLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
@else
    <a class="nav-link collapsed text-chl" href="#" data-bs-toggle="collapse" data-bs-target="#programmerLayouts" aria-expanded="false" aria-controls="programmerLayouts">
        <div class="sb-nav-link-icon"><i class="fas fa-file-lines"></i></div>
        Super Admin
        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
    </a>
    <div class="collapse" id="programmerLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
@endif
        <nav class="sb-sidenav-menu-nested nav ">
            @if(Route::currentRouteName() == 'company.setup')
                <a class="nav-link" href="{{route('company.setup')}}"><div class="sb-nav-link-icon"><i class="fas fa-solid fa-file-circle-plus"></i></div> Company Setup</a>
            @else
                <a class="nav-link text-chl" href="{!! route('company.setup') !!}"><div class="sb-nav-link-icon"><i class="fas fa-solid fa-file-circle-plus"></i></div> Company Setup</a>
            @endif
            @if(Route::currentRouteName() == 'edit.company.type')
                    <a class="nav-link" href="{{route('edit.company.type',['companyTypeID'=>\Illuminate\Support\Facades\Request::route('companyTypeID')])}}"><div class="sb-nav-link-icon"><i class='fas fa-edit'></i></div> Company Type Edit</a>
            @endif
            @if(Route::currentRouteName() == 'permission.input')
                <a class="nav-link" href="{{route('permission.input')}}"><div class="sb-nav-link-icon"><i class="fa-solid fa-circle-plus"></i></div> Permission Input</a>
            @else
                <a class="nav-link text-chl" href="{!! route('permission.input') !!}"><div class="sb-nav-link-icon"><i class="fa-solid fa-circle-plus"></i></div> Permission Input</a>
            @endif
        </nav>
    </div>
