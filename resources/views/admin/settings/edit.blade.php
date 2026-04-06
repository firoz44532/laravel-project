@extends('admin.layout')

@section('title', 'Edit Setting')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Setting: {{ $setting->title }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Settings
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update', $setting) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="key" class="form-label">Setting Key</label>
                                    <input type="text" 
                                           id="key" 
                                           name="key" 
                                           value="{{ old('key', $setting->key) }}"
                                           class="form-control @error('key') is-invalid @enderror"
                                           required>
                                    @error('key')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $setting->title) }}"
                                           class="form-control @error('title') is-invalid @enderror"
                                           required>
                                    @error('title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="group" class="form-label">Group</label>
                                    <select id="group" 
                                            name="group" 
                                            class="form-control @error('group') is-invalid @enderror"
                                            required>
                                        <option value="">Select Group</option>
                                        @foreach($groups as $group)
                                            <option value="{{ $group }}" {{ old('group', $setting->group) == $group ? 'selected' : '' }}>
                                                {{ ucfirst($group) }}
                                            </option>
                                        @endforeach
                                        <option value="general" {{ old('group', $setting->group) == 'general' ? 'selected' : '' }}>
                                            General
                                        </option>
                                    </select>
                                    @error('group')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type" class="form-label">Type</label>
                                    <select id="type" 
                                            name="type" 
                                            class="form-control @error('type') is-invalid @enderror"
                                            required>
                                        <option value="">Select Type</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type }}" {{ old('type', $setting->type) == $type ? 'selected' : '' }}>
                                                {{ ucfirst($type) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="value" class="form-label">Value</label>
                                    @if($setting->type == 'boolean')
                                        <div class="form-check">
                                            <input type="hidden" name="value" value="0">
                                            <input type="checkbox" 
                                                   id="value" 
                                                   name="value" 
                                                   value="1"
                                                   {{ $setting->getValue() ? 'checked' : '' }}
                                                   class="form-check-input">
                                            <label class="form-check-label" for="value">
                                                Enable {{ $setting->title }}
                                            </label>
                                        </div>
                                    @elseif($setting->type == 'number')
                                        <input type="number" 
                                               id="value" 
                                               name="value" 
                                               value="{{ old('value', $setting->getValue()) }}"
                                               class="form-control @error('value') is-invalid @enderror">
                                    @elseif($setting->type == 'json')
                                        <textarea id="value" 
                                                  name="value" 
                                                  class="form-control @error('value') is-invalid @enderror"
                                                  rows="3">{{ old('value', $setting->value) }}</textarea>
                                    @else
                                        <input type="text" 
                                               id="value" 
                                               name="value" 
                                               value="{{ old('value', $setting->value) }}"
                                               class="form-control @error('value') is-invalid @enderror">
                                    @endif
                                    @error('value')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" 
                                           id="sort_order" 
                                           name="sort_order" 
                                           value="{{ old('sort_order', $setting->sort_order) }}"
                                           class="form-control @error('sort_order') is-invalid @enderror"
                                           min="0">
                                    @error('sort_order')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" 
                                      name="description" 
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="3">{{ old('description', $setting->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" 
                                       id="is_public" 
                                       name="is_public" 
                                       value="1"
                                       {{ old('is_public', $setting->is_public) ? 'checked' : '' }}
                                       class="form-check-input">
                                <label class="form-check-label" for="is_public">
                                    Make this setting public (accessible on frontend)
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Setting
                            </button>
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
