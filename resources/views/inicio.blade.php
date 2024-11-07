@extends('principal')
@section('titulo', 'INICIO')
@section('contenido')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="row d-flex justify-content-center border-dashed-bottom pb-3">
                        <div class="col-9">
                            <p class="text-dark mb-0 fw-semibold fs-14">Sessions</p>
                            <h3 class="mt-2 mb-0 fw-bold">24k</h3>
                        </div>
                        <!--end col-->
                        <div class="col-3 align-self-center">
                            <div
                                class="d-flex justify-content-center align-items-center thumb-xl bg-light rounded-circle mx-auto">
                                <i class="iconoir-hexagon-dice h1 align-self-center mb-0 text-secondary"></i>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                    <p class="mb-0 text-truncate text-muted mt-3"><span class="text-success">8.5%</span>
                        New Sessions Today</p>
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="row d-flex justify-content-center border-dashed-bottom pb-3">
                        <div class="col-9">
                            <p class="text-dark mb-0 fw-semibold fs-14">Avg.Sessions</p>
                            <h3 class="mt-2 mb-0 fw-bold">00:18</h3>
                        </div>
                        <!--end col-->
                        <div class="col-3 align-self-center">
                            <div
                                class="d-flex justify-content-center align-items-center thumb-xl bg-light rounded-circle mx-auto">
                                <i class="iconoir-clock h1 align-self-center mb-0 text-secondary"></i>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                    <p class="mb-0 text-truncate text-muted mt-3"><span class="text-success">1.5%</span>
                        Weekly Avg.Sessions</p>
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="row d-flex justify-content-center border-dashed-bottom pb-3">
                        <div class="col-9">
                            <p class="text-dark mb-0 fw-semibold fs-14">Bounce
                                Rate</p>
                            <h3 class="mt-2 mb-0 fw-bold">36.45%</h3>
                        </div>
                        <!--end col-->
                        <div class="col-3 align-self-center">
                            <div
                                class="d-flex justify-content-center align-items-center thumb-xl bg-light rounded-circle mx-auto">
                                <i class="iconoir-percentage-circle h1 align-self-center mb-0 text-secondary"></i>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                    <p class="mb-0 text-truncate text-muted mt-3"><span class="text-danger">8%</span>
                        Up Bounce Rate Weekly</p>
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
@endsection
