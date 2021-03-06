@extends('template')
@section('page_title')
	Settings
@stop
@section('content')
	@include('errors')
<!-- BEGIN Content -->
<div id="main-content">
	<div class="row">
	    <div class="col-md-12">
	        <div class="box box-black">
	            <div class="box-title">
	                <h3><i class="fa fa-table"></i> Settings Table</h3>
	                <div class="box-tool">
	                    <a data-action="collapse" href="#"><i class="fa fa-chevron-up"></i></a>
	                    <a data-action="close" href="#"><i class="fa fa-times"></i></a>
	                </div>
	            </div>
	            <div class="box-content">
					<div class="btn-toolbar pull-right">
						<div class="btn-group">
							<a class="btn btn-circle show-tooltip" title="Add Setting" href="{{url('setting/new')}}" data-original-title="Add new record"><i class="fa fa-plus"></i></a>
							<a  id="delete_button" onclick="delete_selected('settings')" class="btn btn-circle btn-danger show-tooltip" title="@lang('messages.template.delete_many')" href="#"><i class="fa fa-trash-o"></i></a>
						</div>
					</div>
					<br><br>
					<div class="table-responsive">
						<table class="table table-advance">
						<thead>
							<tr>
								<th style="width:18px"><input type="checkbox"></th>
								<th>Key</th>
								<th>Value</th>
								{{-- <th>Created at</th> --}}
								<th class="visible-md visible-lg" style="width:130px">Action</th>
							</tr>
						</thead>
						<tbody>
						@foreach($settings as $setting)
							<tr class="table-flag-blue">
								<td><input type="checkbox" name="selected_rows[]" value="{{$setting->id}}" onclick="collect_selected(this)"></td>
								<td>{{$setting->key}}</td>
								<td>
									@if(file_exists($setting->value))
                                     @if($setting->type == "3")
										<img src="{{url($setting->value)}}" width="300" height="225">
                                     @elseif($setting->type == "4")
                                       <video controls width="300" height="225"
                                        src="{{url($setting->value)}}">
                                        </video>
                                     @elseif($setting->type == "5")
                                       <audio controls="">
                                            <source src="{{url($setting->value)}}" type="audio/mpeg">
                                        </audio>
                                     @endif
									@else
										{!! $setting->value !!}
									@endif
								</td>
								{{-- <td>{{$setting->created_at}}</td> --}}
								<td class="visible-md visible-lg">
								    <div class="btn-group">
								    	<a class="btn btn-sm show-tooltip" title="" href="{{url('setting/'.$setting->id.'/edit')}}" data-original-title="Edit"><i class="fa fa-edit"></i></a>
								        <a class="btn btn-sm btn-danger show-tooltip" title="" onclick = 'return ConfirmDelete()' href="{{url('setting/'.$setting->id.'/delete')}}" data-original-title="Delete"><i class="fa fa-trash-o"></i></a>
								    </div>
								</td>
							</tr>
						@endforeach
						</tbody>
						</table>
					</div>
	            </div>
	        </div>
	    </div>
	</div>
</div>

@stop
@section('script')
	<script>
        var videos = document.querySelectorAll('video');
        for(var i=0; i<videos.length; i++)
           videos[i].addEventListener('play', function(){pauseAll(this)}, true);
        function pauseAll(elem){
            for(var i=0; i<videos.length; i++){
                //Is this the one we want to play?
                if(videos[i] == elem) continue;
                //Have we already played it && is it already paused?
                if(videos[i].played.length > 0 && !videos[i].paused){
                // Then pause it now
                  videos[i].pause();
                  
                }
            }
          }
        $(document).ready(function() {
            var audioElement = document.createElement('audio');
            audioElement.addEventListener('ended', function() {
                 this.play();
             }, false);   
        });
        
        document.addEventListener('play', function(e){
        var audios = document.getElementsByTagName('audio');
        for(var i = 0, len = audios.length; i < len;i++){
        if(audios[i] != e.target){
            audios[i].pause();
        }
        }
        }, true);
        
        document.addEventListener('play', function(e){
        var videos = document.getElementsByTagName('video');
        for(var i = 0, len = videos.length; i < len;i++){
        if(videos[i] != e.target){
            videos[i].pause();
        }
        }
        }, true);
		$('#setting').addClass('active');
		$('#setting-index').addClass('active');
	</script>
@stop