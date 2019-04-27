<body>
<div class="container"> 

<!-- Display status message -->
    <?php if(!empty($success_msg)){ ?>
        <div class="col-xs-12">
            <div class="alert alert-success"><?php echo $success_msg; ?></div>
        </div>
    <?php } ?>
    <?php if(!empty($error_msg)){ ?>
        <div class="col-xs-12">
            <div class="alert alert-danger"><?php echo $error_msg; ?></div>
        </div>
    <?php } ?>

    <div class="row"> 
            <!-- Import link -->
        <div class="col-md-12 head">
            <div class="float-right">
                <a href="javascript:void(0);" class="btn btn-success" onclick="formToggle('importFrm');"><i class="plus"></i> Import</a>
            </div>
        </div>

            <!-- File upload form -->
        <div class="col-md-12" id="importFrm" style="display: none;">
            <form action="<?php echo base_url('/importData'); ?>" method="post" enctype="multipart/form-data">
                <input type="file" name="file" />
                <input type="submit" class="btn btn-primary" name="importSubmit" value="IMPORT">
            </form>
        </div>

        <table id="clients_deals" class="display">
            <thead class="text-center">
                <th>ClientDeal ID</th>
                <th>Client ID</th>
                <th>Client Name</th>
                <th>Deal ID</th>
                <th>Deal Name</th>
                <th>Date</th>
                <th>Accepted</th>
                <th>Refused</th>
            </thead>
            <tbody>
            <?php foreach ($clientDeal as $cd) : ?>
                <tr class="text-center" >
                    <td> <?php echo $cd['id'] ?> </td>
                    <td> <?php echo $cd['cid'] ?> </td>
                    <td> <?php echo $cd['cname'] ?> </td>
                    <td> <?php echo $cd['did'] ?> </td>
                    <td> <?php echo $cd['dname'] ?> </td>
                    <td> <?php echo $cd['hour'] ?> </td>
                    <td> <?php echo $cd['accepted'] ?> </td>
                    <td> <?php echo $cd['refused'] ?> </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>
<script>
function formToggle(ID){
    var element = document.getElementById(ID);
    if(element.style.display === "none"){
        element.style.display = "block";
    }else{
        element.style.display = "none";
    }
}
</script>
<script type="text/javascript" charset="utf8" src="<?php echo base_url(); ?>assets/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="<?php echo base_url(); ?>assets/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready( function () {
    $('#clients_deals').DataTable();
} );
</script>
