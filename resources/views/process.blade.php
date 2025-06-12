@extends('layouts.app')

@section('title', 'Homepage')

@section('content')
<section class="wrapper bg-light">
  <div class="container py-14 py-md-16">
    <h2 class="display-4 mb-3">How We Do It?</h2>
    <p class="lead fs-lg mb-8">We make your spending <span class="underline">stress-free</span> for you to have the perfect control.</p>
    <div class="row gx-lg-8 gx-xl-12 gy-6 process-wrapper line">
      <div class="col-md-6 col-lg-3"> <span class="icon btn btn-circle btn-lg btn-soft-primary pe-none mb-4"><span class="number">01</span></span>
        <h4 class="mb-1">Concept</h4>
        <p class="mb-0">Nulla vitae elit libero elit non porta gravida eget metus cras. Aenean eu leo quam. Pellentesque ornare.</p>
      </div>
      <!--/column -->
      <div class="col-md-6 col-lg-3"> <span class="icon btn btn-circle btn-lg btn-primary pe-none mb-4"><span class="number">02</span></span>
        <h4 class="mb-1">Prepare</h4>
        <p class="mb-0">Vestibulum id ligula porta felis euismod semper. Sed posuere consectetur est at lobortis.</p>
      </div>
      <!--/column -->
      <div class="col-md-6 col-lg-3"> <span class="icon btn btn-circle btn-lg btn-soft-primary pe-none mb-4"><span class="number">03</span></span>
        <h4 class="mb-1">Retouch</h4>
        <p class="mb-0">Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Nulla vitae elit libero.</p>
      </div>
      <!--/column -->
      <div class="col-md-6 col-lg-3"> <span class="icon btn btn-circle btn-lg btn-soft-primary pe-none mb-4"><span class="number">04</span></span>
        <h4 class="mb-1">Finalize</h4>
        <p class="mb-0">Integer posuere erat, consectetur adipiscing elit. Fusce dapibus, tellus ac cursus commodo.</p>
      </div>
      <!--/column -->
    </div>
    <!--/.row -->
  </div>
  <!-- /.container -->
</section>
<!-- /section -->
@endsection
