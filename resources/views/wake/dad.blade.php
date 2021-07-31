@extends('layouts.app')

@section('content')
    <div class="col-md-10 mx-auto">
        <div class="row">
            <div class="col-md-2 bg-dark">
                <ul class="nav nav-tabs align-content-center flex-column">
                    <li class="nav-item active"><a class="nav-link" data-toggle="tab" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#abc">ABC</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#def">DEF</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#ghq">GHQ</a></li>
                </ul>
            </div>
            <div class="col-md-10 bg-success">
                <div class="tab-content">
                    <div id="home" class="tab-pane active">
                        <h3>Home</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Corporis, tempora.</p>
                    </div>
                    <div id="abc" class="tab-pane fade">
                        <h3>abc</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Corporis, tempora.</p>
                    </div>
                    <div id="def" class="tab-pane fade">
                        <h3>def</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Corporis, tempora.</p>
                    </div>
                    <div id="ghq" class="tab-pane fade">
                        <h3>ghq</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Corporis, tempora.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
