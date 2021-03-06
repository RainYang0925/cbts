<?php include('common_header.php');?>
    <div class="pageheader">
      <h2><i class="fa fa-pencil"></i> 我的任务 <span>任务列表</span></h2>
      <div class="breadcrumb-wrapper">
        <span class="label">你的位置:</span>
        <ol class="breadcrumb">
          <li><a href="/">我的控制台</a></li>
          <li><a href="/issue/my">我的任务</a></li>
          <li class="active">任务列表</li>
        </ol>
      </div>
    </div>
    
    <div class="contentpanel">

      <div class="row">
        <div class="col-md-12">
            
            <div class="panel panel-dark panel-alt">
                <div class="panel-heading">
                    <div class="panel-btns">
                        <a href="" class="panel-close">&times;</a>
                        <a href="" class="minimize">&minus;</a>
                    </div><!-- panel-btns -->
                    <h5 class="panel-title">任务列表</h5>
                </div><!-- panel-heading -->
                <div class="panel-body panel-table">
                    <div class="table-responsive">
                    <table class="table">
                      <thead>
                        <tr class="table-head-alt">
                          <th>#</th>
                          <th>名称</th>
                          <th>受理进度</th>
                          <th>状态</th>
                          <th>最后修改</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          if ($rows) {
                            foreach ($rows as $value) {
                        ?>
                        <tr id="tr-<?php echo $value['id'];?>">
                          <td><?php echo $value['id'];?></td>
                          <td><?php if ($value['status'] == '-1') { echo '<s><a href="/issue/view/'.$value['id'].'">'.$value['issue_name'].'</a></s>'; } else { echo '<a href="/issue/view/'.$value['id'].'">'.$value['issue_name'].'</a>'; }?></td>
                          <td><?php if ($value['resolve']) { ?> <span class="label label-success">已解决</span><?php } else {?> <span class="label label-info">未解决</span><?php } ?></td>
                          <td>
                            <?php if ($value['status'] == 1) {?> <span class="label label-primary">正常</span><?php }?>
                            <?php if ($value['status'] == 0) {?> <span class="label label-default">已关闭</span><?php }?>
                            <?php if ($value['status'] == -1) {?> <span class="label label-white">已删除</span><?php }?>
                          </td>
                          <td><?php echo $value['last_user'] ? $users[$value['last_user']]['realname'] : '-';?></td>
                          <td class="table-action">
                            <?php if ($value['status'] == 1 && $value['resolve'] == 0) { ?>
                            <a href="/test/add/<?php echo $value['id'];?>"><i class="fa fa-slack"></i> 提交代码</a>
                            <a href="/issue/edit/<?php echo $value['id'];?>"><i class="fa fa-pencil"></i> 编辑</a>
                            <a href="javascript:;" class="delete-row" reposid="<?php echo $value['id'];?>"><i class="fa fa-trash-o"></i> 删除</a>
                            <?php } else { echo "已完成并归档";}?>
                          </td>
                        </tr>
                        <?php
                            }
                          } else {
                        ?>
                          <tr><td colspan="7" align="center">任务列表为空~,<a href="/issue/add">赶紧添加吧</a></td></tr>
                        <?php
                          }
                        ?>
                      </tbody>
                    </table>
                    </div><!-- table-responsive -->
                </div><!-- panel-body -->
                <?php echo $pages;?>
            </div><!-- panel -->
            
        </div><!-- col-md-6 -->
                        
      </div><!-- row -->
      
    </div><!-- contentpanel -->
    
  </div><!-- mainpanel -->
  
</section>

<script src="/static/js/jquery-1.11.1.min.js"></script>
<script src="/static/js/jquery-migrate-1.2.1.min.js"></script>
<script src="/static/js/bootstrap.min.js"></script>
<script src="/static/js/modernizr.min.js"></script>
<script src="/static/js/jquery.sparkline.min.js"></script>
<script src="/static/js/toggles.min.js"></script>
<script src="/static/js/retina.min.js"></script>
<script src="/static/js/jquery.cookies.js"></script>

<script src="/static/js/jquery.datatables.min.js"></script>
<script src="/static/js/select2.min.js"></script>
<script src="/static/js/jquery.gritter.min.js"></script>

<script src="/static/js/custom.js"></script>
<script>
  $(document).ready(function(){
    $(".delete-row").click(function(){
      var c = confirm("确认要删除吗？");
      if(c) {
        id = $(this).attr("reposid");
        $.ajax({
          type: "GET",
          url: "/issue/del/"+id,
          dataType: "JSON",
          success: function(data){
            if (data.status) {
              $("#tr-"+id).fadeOut(function(){
                $("#tr-"+id).remove();
              });
              return false;
            } else {
              jQuery.gritter.add({
                title: '提醒',
                text: data.message,
                  class_name: 'growl-danger',
                  image: '/static/images/screen.png',
                sticky: false,
                time: ''
              });
            };
          }
        });
      }
    });
  });
</script>

</body>
</html>
