<section class="page-top">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="breadcrumb">
                <?php                    
                    if(function_exists('bcn_display')) {
                        bcn_display_list();
                    }  
                ?>
                </ul>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<h2><?php the_title(); ?></h2>
			</div>
		</div>
	</div>
</section>