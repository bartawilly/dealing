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
    <?php if(!empty($error_credentials)){ ?>
        <div class="col-xs-12">
            <div class="alert alert-danger"><?php echo $error_credentials; ?></div>
        </div>
    <?php } ?>
    

    <div class="row"> 
            <!-- Import link -->
        <div class="col-md-6">
                <a href="javascript:void(0);" class="btn btn-success" onclick="formToggle('importFrm');"><i class="plus"></i> Import</a>
        </div>
        <!-- Initialize link -->
        <div class="col-md-6">
                <a href="javascript:void(0);" class="btn btn-success" onclick="formToggle('InitializeFrm');"><i class="plus"></i> Initialize</a>
        </div>

            <!-- File upload form -->
        <div class="col-md-6" id="importFrm" style="display: none;">
            <form action="<?php echo base_url('/importData'); ?>" method="post" enctype="multipart/form-data">
                <input type="file" name="file" />
                <input type="submit" class="btn btn-primary" name="importSubmit" value="IMPORT">
            </form>
        </div>

        <!-- Initialize username & password form -->
        <div class="col-md-6" id="InitializeFrm" style="display: none;">
            <form action="<?php echo base_url('/intialize'); ?>" method="post" enctype="multipart/form-data">
                <label for="username"><b>MySQL Username</b></label>
                <input type="text" placeholder="Enter Username" name="username" required>
                <br>
                <label for="password"><b>MySQL Password</b></label>
                <input type="password" placeholder="Enter Password" name="password">

                <input type="submit" class="btn btn-primary" name="intializeSubmit" value="Initialize">
            </form>
        </div>
        <!-- table will show all the clients deals -->
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
            <!-- check if the clientDeal array is retrieved -->
            <?php if(isset($clientDeal)) : ?>
            <!-- loop to show each row contains the client deal -->
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
            <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>
<!-- script for hide and show the form onclick on the btn -->
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
<!-- user the dataTables plug-in to do some operation on the viewed table sorting, filtring or search -->
<script type="text/javascript" charset="utf8" src="<?php echo base_url(); ?>assets/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="<?php echo base_url(); ?>assets/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready( function () {
    $('#clients_deals').DataTable();
} );
</script>
