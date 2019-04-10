@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group ">
                @can('system.permission.destroy')
                    <button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删 除</button>
                @endcan
                @can('system.permission.create')
                    <a class="layui-btn layui-btn-sm" href="{{ route('admin.permission.create') }}">添 加</a>
                @endcan
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable" lay-filter="treeTable"></table>
            <script type="text/html" id="icon">
                <i class="layui-icon @{{ d.icon.class }}"></i>
            </script>
            <script type="text/html" id="options">
                {{--<div class="layui-btn-group"></div>--}}
                    {{--@can('system.permission')--}}
                        {{--<a class="layui-btn layui-btn-sm" lay-event="children">子权限</a>--}}
                    {{--@endcan--}}
                    @can('system.permission.edit')
                        <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    @can('system.permission.destroy')
                        <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">删除</a>
                    @endcan

            </script>
        </div>
    </div>
@endsection

@section('script')
    @can('system.permission')
    <script>
        layui.config({
            base: '/static/admin/layuiadmin/layui/extend/'
        }).extend({
            treeGrid:'treeGrid'
        }).use(['layer','treeGrid','table','form'],function () {
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.treeGrid;
            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 500
                ,url: "{{ route('admin.data') }}"+'?model=permission' //数据接口
                ,where:{model:"permission",limit:999}
                ,method:'GET'
                ,treeId:'id'//树形id字段名称
                ,treeUpId:'parent_id'//树形父id字段名称
                ,treeShowName:'display_name'//以树形式显示的字段
                ,isFilter: false
                ,iconOpen: true//是否显示图标【默认显示】
                ,isOpenDefault: false//节点默认是展开还是折叠【默认展开】
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'name', title: '权限名称'}
                    ,{field: 'display_name', title: '显示名称',minWidth:100}
                    ,{field: 'route', title: '路由'}
                    ,{field: 'icon_id', title: 'ico', toolbar:'#icon',width:50}
                    ,{field: 'created_at', title: '创建时间'}
                    ,{field: 'updated_at', title: '更新时间'}
                    ,{fixed: 'right', align:'center', toolbar: '#options', minWidth: 100,}
                ]]
                ,page: false //分页
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        $.post("{{ route('admin.permission.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                            if (result.code==0){
                                obj.del(); //删除对应行（tr）的DOM结构
                            }
                            layer.close(index);
                            layer.msg(result.msg,{icon:6})
                        });
                    });
                } else if(layEvent === 'edit'){
                    location = '/admin/permission/'+data.id+'/edit';
                    //编辑
                    /*var index = layer.open({
                        type: 2,
                        content: '/admin/permission/'+data.id+'/edit',
                        area: ['600px', '500px'],
                        maxmin: true
                    });
                    if ( window.innerWidth <= 700) {
                        layer.full(index);
                    }*/

                } else if (layEvent === 'children'){
                    var pid = $("#returnParent").attr("pid");
                    if (data.parent_id!=0){
                        $("#returnParent").attr("pid",pid+'_'+data.parent_id);
                    }
                    dataTable.reload({
                        where:{model:"permission",parent_id:data.id},
                        page:{curr:1}
                    })
                }
            });

            //按钮批量删除
            $("#listDelete").click(function () {
                layer.msg("由于权限重要性，系统已禁止批量删除",{icon:5});
                /*var ids = []
                var hasCheck = table.checkStatus('dataTable')
                var hasCheckData = hasCheck.data
                if (hasCheckData.length>0){
                    $.each(hasCheckData,function (index,element) {
                        ids.push(element.id)
                    })
                }
                if (ids.length>0){
                    layer.confirm('确认删除吗？', function(index){
                        $.post("{{ route('admin.permission.destroy') }}",{_method:'delete',ids:ids},function (result) {
                            if (result.code==0){
                                dataTable.reload()
                            }
                            layer.close(index);
                            layer.msg(result.msg,{icon:6})
                        });
                    })
                }else {
                    layer.msg('请选择删除项',{icon:5})
                }*/
            });
        })
    </script>
    @endcan
@endsection