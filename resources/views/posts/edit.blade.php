@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Post</h1>
    <form action="{{ url('/admin/posts/' . $post->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ $post->title }}" required>
        </div>
        <div class="form-group">
            <label for="author">Author</label>
            <input type="text" class="form-control" id="author" name="author" value="{{ $post->author }}" required>
        </div>
        <div class="form-group">
            <label for="breadcrumb">Breadcrumb</label>
            <input type="text" class="form-control" id="breadcrumb" name="breadcrumb" value="{{ $post->breadcrumb }}">
        </div>
        <div class="form-group">
            <label for="body_content">Content</label>
            <textarea class="form-control" id="body_content" name="body_content" rows="5" required>{{ $post->body_content }}</textarea>
        </div>
        <div class="form-group">
            <label for="featured_image">Featured Image</label>
            <input type="file" class="form-control" id="featured_image" name="featured_image">
        </div>
        <div class="form-group">
            <label for="categories">Categories</label>
            <input type="text" class="form-control" id="categories" name="categories" value="{{ $post->categories }}">
        </div>
        <div class="form-group">
            <label for="slug">Slug</label>
            <input type="text" class="form-control" id="slug" name="slug" value="{{ $post->slug }}" required>
        </div>
        <div class="form-group">
            <label for="video_url">Video URL</label>
            <input type="text" class="form-control" id="video_url" name="video_url" value="{{ $post->video_url }}">
        </div>
        <button type="submit" class="btn btn-primary">Update Post</button>
    </form>
</div>
@endsection