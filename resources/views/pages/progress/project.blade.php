@extends('layouts.app')

@section('title', 'Project Page')

@section('content')

<div class="page-heading">
    <div class="page-content">
        <section id="basic-horizontal-layouts">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm-8 col-8">
                            <h1>Progress <span class="d-none d-md-inline-block">Proyek</span></h1>
                        </div>
                        <div class="col-sm-4 col-4 d-flex justify-content-end align-items-center">
                            <a href="{{ route('progress.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-angle-left"></i> <span class="d-none d-md-inline-block">Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-striped" id="table">
                                <thead>
                                    <tr>
                                        <th>Spec Tech</th>
                                        <th width="15%" class="text-center">Progress</th>
                                        <th width="15%" class="text-center">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($activityCategory as $cat)
                                        <tr>
                                            <td>{{ $cat['name'] }}</td>
                                            <td class="text-center">
                                                <input type="text" class="form-control" id="progress_{{ $cat['id'] }}" name="progress_{{ $cat['id'] }}">
                                            </td>
                                            <td width="15%">
                                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="2"></textarea>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-12 d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary me-1 mb-1" id="submitButtonPage1">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@endsection