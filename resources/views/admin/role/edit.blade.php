@extends('admin.master')

@section('mainContent')
<style>
    .switch {
        display: inline-block;
        height: 34px;
        position: relative;
        width: 60px;
    }

    .switch input {
        display:none;
    }

    .slider {
        background-color: #ccc;
        bottom: 0;
        cursor: pointer;
        left: 0;
        position: absolute;
        right: 0;
        top: 0;
        transition: .4s;
    }

    .slider:before {
        background-color: #fff;
        bottom: 4px;
        content: "";
        height: 26px;
        left: 4px;
        position: absolute;
        transition: .4s;
        width: 26px;
    }

    input:checked + .slider {
        background-color: #66bb6a;
    }

    input:checked + .slider:before {
        transform: translateX(26px);
    }

    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>

    <div class="content">
        <!-- END: Top Bar -->
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                Edit Role
            </h2>
        </div>
        <div class="grid grid-cols-12 gap-6 mt-5">

            <div class="intro-y col-span-12 lg:col-span-6">
                <!-- BEGIN: Input -->
                <div class="intro-y box">
                    <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">


                    </div>
                    {!! Form::open(['url' => 'role/update/'.$role->id, 'method'=>'post', 'role'=>'form', 'files' => true, 'enctype'=>'multipart/form-data' ]) !!}

                    <div id="input" class="p-5">
                        <div class="preview">

                            <div class="mt-3">
                                <label for="regular-form-2" class="form-label">Name</label>
                                <input id="regular-form-2" type="text" class="form-control form-control-rounded" placeholder="name" name="name" required disabled value="{{$role->name}}">
                            </div>
                        </div>

                    </div>
                </div>


                <div class="source-code hidden">
                    <button data-target="#copy-checkbox-switch" class="copy-code btn py-1 px-2 btn-outline-secondary"> <i data-feather="file" class="w-4 h-4 mr-2"></i> Copy example code </button>
                    <div class="overflow-y-auto mt-3 rounded-md">
                        <pre id="copy-checkbox-switch" class="source-preview"> <code class="text-xs p-0 rounded-md html pl-5 pt-8 pb-4 -mb-10 -mt-10"> HTMLOpenTagdivHTMLCloseTag HTMLOpenTaglabelHTMLCloseTagVertical CheckboxHTMLOpenTag/labelHTMLCloseTag HTMLOpenTagdiv class=&quot;form-check mt-2&quot;HTMLCloseTag HTMLOpenTaginput id=&quot;checkbox-switch-1&quot; class=&quot;form-check-input&quot; type=&quot;checkbox&quot; value=&quot;&quot;HTMLCloseTag HTMLOpenTaglabel class=&quot;form-check-label&quot; for=&quot;checkbox-switch-1&quot;HTMLCloseTagChris EvansHTMLOpenTag/labelHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTagdiv class=&quot;form-check mt-2&quot;HTMLCloseTag HTMLOpenTaginput id=&quot;checkbox-switch-2&quot; class=&quot;form-check-input&quot; type=&quot;checkbox&quot; value=&quot;&quot;HTMLCloseTag HTMLOpenTaglabel class=&quot;form-check-label&quot; for=&quot;checkbox-switch-2&quot;HTMLCloseTagLiam NeesonHTMLOpenTag/labelHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTagdiv class=&quot;form-check mt-2&quot;HTMLCloseTag HTMLOpenTaginput id=&quot;checkbox-switch-3&quot; class=&quot;form-check-input&quot; type=&quot;checkbox&quot; value=&quot;&quot;HTMLCloseTag HTMLOpenTaglabel class=&quot;form-check-label&quot; for=&quot;checkbox-switch-3&quot;HTMLCloseTagDaniel CraigHTMLOpenTag/labelHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTagdiv class=&quot;mt-3&quot;HTMLCloseTag HTMLOpenTaglabelHTMLCloseTagHorizontal CheckboxHTMLOpenTag/labelHTMLCloseTag HTMLOpenTagdiv class=&quot;flex flex-col sm:flex-row mt-2&quot;HTMLCloseTag HTMLOpenTagdiv class=&quot;form-check mr-2&quot;HTMLCloseTag HTMLOpenTaginput id=&quot;checkbox-switch-4&quot; class=&quot;form-check-input&quot; type=&quot;checkbox&quot; value=&quot;&quot;HTMLCloseTag HTMLOpenTaglabel class=&quot;form-check-label&quot; for=&quot;checkbox-switch-4&quot;HTMLCloseTagChris EvansHTMLOpenTag/labelHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTagdiv class=&quot;form-check mr-2 mt-2 sm:mt-0&quot;HTMLCloseTag HTMLOpenTaginput id=&quot;checkbox-switch-5&quot; class=&quot;form-check-input&quot; type=&quot;checkbox&quot; value=&quot;&quot;HTMLCloseTag HTMLOpenTaglabel class=&quot;form-check-label&quot; for=&quot;checkbox-switch-5&quot;HTMLCloseTagLiam NeesonHTMLOpenTag/labelHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTagdiv class=&quot;form-check mr-2 mt-2 sm:mt-0&quot;HTMLCloseTag HTMLOpenTaginput id=&quot;checkbox-switch-6&quot; class=&quot;form-check-input&quot; type=&quot;checkbox&quot; value=&quot;&quot;HTMLCloseTag HTMLOpenTaglabel class=&quot;form-check-label&quot; for=&quot;checkbox-switch-6&quot;HTMLCloseTagDaniel CraigHTMLOpenTag/labelHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTagdiv class=&quot;mt-3&quot;HTMLCloseTag HTMLOpenTaglabelHTMLCloseTagSwitchHTMLOpenTag/labelHTMLCloseTag HTMLOpenTagdiv class=&quot;mt-2&quot;HTMLCloseTag HTMLOpenTagdiv class=&quot;form-check form-switch&quot;HTMLCloseTag HTMLOpenTaginput id=&quot;checkbox-switch-7&quot; class=&quot;form-check-input&quot; type=&quot;checkbox&quot;HTMLCloseTag HTMLOpenTaglabel class=&quot;form-check-label&quot; for=&quot;checkbox-switch-7&quot;HTMLCloseTagDefault switch checkbox inputHTMLOpenTag/labelHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTag/divHTMLCloseTag HTMLOpenTag/divHTMLCloseTag </code> </pre>
                    </div>
                </div>
            </div>
        </div>
        {{--<button class="btn btn-primary mt-5">Хадгалах</button>--}}
        <br>
        <div class="intro-y col-span-12 lg:col-span-6">
            <!-- BEGIN: Input -->
            <div class="intro-y box">
                <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                Permissions
                </div>
                <div id="input" class="p-5">
                    <div class="col-md-8" id="main">

                        @foreach($categories as $category)

                            <h1><b>{{ ucwords($category->name)}}</b></h1>
                            <br>
                            @foreach($category->permissions as $permission)
                                <div class="sm:col-span-2">
                                        <div class="col-span-6 sm:col-span-6">
                                            {{ ucwords(str_replace('_',' ',$permission->name)) }}
                                        </div>
                                        <div class="col-span-6 sm:col-span-6" style="text-align: right;">
                                            <label class="switch" for="checkbox_{{$permission->id}}">
                                                <input type="checkbox" id="checkbox_{{$permission->id}}"  name="permissions[]" value="{{$permission->id}}" {{ in_array($permission->id,$roleper) ? 'checked' : '' }}/>
                                                <div class="slider round"></div>
                                            </label>
                                        </div>
                                </div>

                            @endforeach
                            <hr style="padding: 5px">
                        @endforeach
                </div>
            </div>
        </div>
    <button class="btn btn-primary mt-5">Хадгалах</button>

    {!! Form::close()!!}
    <!-- END: Checkbox & Switch -->
        <!-- BEGIN: Radio Button -->

        <!-- END: Radio Button -->
    </div>

    <!-- BEGIN: Top Bar -->

    <!-- END: Top Bar -->

@endsection
