@extends('journal::layouts.app')

@section('content')
    <style>
        main {
            padding: 0 !important;
            height: calc(100vh - 56px);
            background-color: #f5f7fa;
        }

        .layout-body {
            display: flex;
            height: 100%;
        }

        .sidebar {
            width: 300px;
            background-color: #fff;
            border-right: 1px solid #ddd;
            overflow-y: auto;
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.05);
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            font-size: 15px;
            color: #495057;
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
        }

        .sidebar .nav-link:hover {
            background-color: #e7f1ff;
            color: #0d6efd;
        }

        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: #fff;
            border-left-color: #0d6efd;
        }

        .submenu {
            padding-left: 1.5rem;
            background-color: #f9fbff;
        }

        .submenu .nav-link {
            padding: 8px 20px;
            font-size: 14px;
        }

        .submenu .nav-link:hover {
            background-color: #dbe9ff;
        }

        .content-area {
            flex: 1;
            padding: 2rem;
            background-color: #fff;
            overflow-y: auto;
            text-align: left; /* 保证左对齐 */
        }

        .search-bar {
            margin-bottom: 20px;
            max-width: 420px;
        }

        .log-content-wrapper pre {
            background: #1e1e1e;
            color: #d4d4d4;
            font-family: Consolas, monospace, 'Courier New', monospace;
            font-size: 16px;
            line-height: 1.4;
            border-radius: 6px;
            user-select: text;
            padding: 20px;
            margin: 0;
            white-space: pre-wrap;
            word-break: break-word;
            text-align: left;
            text-indent: 0;
        }

        .pagination a, .pagination span {
            margin-right: 2px;
        }

        .pagination .disabled {
            pointer-events: none;
            opacity: 0.5;
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>

    <main>
        <div class="layout-body">
            <div class="sidebar" id="sidebarMenu">
                <nav class="nav flex-column">
                    <a class="nav-link {{ empty($channel) ? 'active' : '' }}" href="{{ route('journal.home') }}">
                        <i class="bi bi-house-door"></i> 控制台
                    </a>
                    @foreach($logs as $key => $value)
                        <div>
                            <a class="nav-link collapsed d-flex align-items-center justify-content-between"
                               data-bs-toggle="collapse"
                               href="#{{ $key }}"
                               role="button"
                               aria-expanded="{{ $key === $channel ? 'true' : 'false' }}"
                               aria-controls="{{ $key }}">
                                <span><i class="bi bi-folder"></i> {{ $key }}</span>
                                <i class="bi bi-chevron-down"></i>
                            </a>
                            <div class="collapse submenu {{ $key === $channel ? 'show' : '' }}" id="{{ $key }}">
                                <nav class="nav flex-column">
                                    @foreach($value as $k => $v)
                                        <a class="nav-link {{ $v['path'] === $currentLogFile ? 'active' : '' }}"
                                           href="{{ route('journal.home',['channel' => $key,'path' => $v['path']]) }}">
                                            <i class="bi bi-file-earmark me-2"></i>
                                            {{ $v['basename'] }}
                                        </a>
                                    @endforeach
                                </nav>
                            </div>
                        </div>
                    @endforeach
                </nav>
            </div>
            <div class="content-area" id="logContent">
                @if($currentLogFile)
                    <div class="search-bar">
                        <form method="GET" action="{{ url()->current() }}" class="d-flex">
                            <input type="hidden" name="channel" value="{{ request('channel') }}">
                            <input type="hidden" name="path" value="{{ request('path') }}">

                            <div class="input-group border rounded-pill px-2 py-1 shadow-sm bg-white" style="width: 100%;">
                                <span class="input-group-text bg-transparent border-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" name="search" value="{{ request('search') }}"
                                       class="form-control border-0 px-1"
                                       placeholder="搜索日志..."
                                       style="box-shadow: none;"
                                       autocomplete="off"/>
                                @if(request('search'))
                                    <span class="input-group-text bg-transparent border-0 p-0">
                                        <a href="{{ url()->current() }}?channel={{ request('channel') }}&path={{ request('path') }}"
                                           class="btn btn-link text-muted m-0 p-0"
                                           style="line-height: 1;">
                                            <i class="bi bi-x-lg"></i>
                                        </a>
                                    </span>
                                @endif
                            </div>
                        </form>
                    </div>
                @endif
                @if($currentLogFile && $logLines)
                    <div class="log-content-wrapper">
                        <pre>{{ implode("\n", array_map('trim', $logLines)) }}</pre>
                    </div>

                    @php
                        $totalPages = ceil($totalLines / $perPage);
                        $pageWindow = 2;
                        $startPage = max(1, $page - $pageWindow);
                        $endPage = min($totalPages, $page + $pageWindow);
                    @endphp

                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                        <div class="text-muted">
                            第 {{ $page }} 页，共 {{ $totalPages }} 页（共 {{ $totalLines }} 行）
                        </div>
                        <div class="pagination">
                            @if($page > 1)
                                <a href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}" class="btn btn-outline-primary btn-sm">上一页</a>
                            @else
                                <span class="btn btn-outline-primary btn-sm disabled">上一页</span>
                            @endif

                            @if($startPage > 1)
                                <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}" class="btn btn-outline-primary btn-sm">1</a>
                                @if($startPage > 2)
                                    <span class="px-2">...</span>
                                @endif
                            @endif

                            @for ($i = $startPage; $i <= $endPage; $i++)
                                @if($i == $page)
                                    <span class="btn btn-primary btn-sm disabled">{{ $i }}</span>
                                @else
                                    <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}" class="btn btn-outline-primary btn-sm">{{ $i }}</a>
                                @endif
                            @endfor

                            @if($endPage < $totalPages)
                                @if($endPage < $totalPages - 1)
                                    <span class="px-2">...</span>
                                @endif
                                <a href="{{ request()->fullUrlWithQuery(['page' => $totalPages]) }}" class="btn btn-outline-primary btn-sm">{{ $totalPages }}</a>
                            @endif

                            @if($page < $totalPages)
                                <a href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}" class="btn btn-outline-primary btn-sm">下一页</a>
                            @else
                                <span class="btn btn-outline-primary btn-sm disabled">下一页</span>
                            @endif
                        </div>
                    </div>
                @elseif($currentLogFile)
                    <p class="text-muted">日志为空或读取失败。</p>
                @else
                    <h4>欢迎使用Laravel-Journal日志管理系统</h4>
                    <br>
                    <h5>请点击左侧菜单查看日志内容。</h5>
                @endif
            </div>
        </div>
    </main>
@endsection
