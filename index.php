<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="icon" href="<?php bloginfo('template_directory'); ?>/images/favicon.png""/>
	<link href='https://fonts.googleapis.com/css?family=Roboto:300,400,700,500&subset=latin,vietnamese,latin-ext' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Raleway:400,700' rel='stylesheet' type='text/css'/>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/bootstrap.min.css" type="text/css" media="screen"/>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/font-awesome.min.css" type="text/css" media="screen"/>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/animate.css" type="text/css" media="screen"/>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/style.css" type="text/css" media="screen"/>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/format.css" type="text/css" media="screen"/>
	<?php wp_head(); ?>
</head>
<body>
	<section class="home">
        <div class="container"> 		
			<div class="content-blocks portfolio hidex">
				<section class="content">
					<div class="block-content">
						<h3 class="block-title">Dự án</h3>
	<?php
	$layout = get_option('theme_layout');
	$facebook_url = get_option('facebook_url');
	$twitter_url = get_option('twitter_url');
	echo $layout;
	echo $facebook_url;
	?>
						<div class="row">
							<div class="col-md-12">
								<ul id="filters">
								  <li class="active" data-filter="*">Tất cả</li>
								  <?php $args = array( 'hide_empty' => 0,'taxonomy' => 'danh-muc',); 
							        $cates = get_categories( $args ); 
							        foreach ( $cates as $cate ) {  ?>
							  		<li data-filter=".<?php echo $cate->slug; ?>"><?php echo $cate->name ?></li>
								  <?php } ?>
								</ul>
								<div id="projects">
									<?php $getposts = new WP_query(); $getposts->query('post_status=publish&showposts=-1&post_type=du-an'); ?>
									<?php global $wp_query; $wp_query->in_the_loop = true; ?>
									<?php while ($getposts->have_posts()) : $getposts->the_post(); ?>
										<?php $categories = get_the_terms( get_the_id(), 'danh-muc' ); ?>
										<div class="project <?php foreach ($categories as $value) { echo ' '.$value->slug; } ?> ">
											<a class="open-project" data-id="<?php the_id(); ?>">
												<div class="project-overlay">
													<div class="vcenter">
														<div class="centrize">
															<h4><?php the_title(); ?></h4>                
															<p><?php foreach ($categories as $key => $value) { if($key==0) { echo $value->name; } else { echo ' / '.$value->name; } } ?></p>                
														</div>
													</div>
												</div>
											</a>
											<?php show_thumb(370,257,1,'c'); ?>
										</div>
									<?php endwhile; wp_reset_postdata(); ?>
								</div>
							</div>	
						</div>
					</div>
				</section>
			</div>
			<div class="content-blocks blog hidex">
				<section class="content"> 
					<div class="block-content">  	  
						<h3 class="block-title">Blog của tôi</h3>
						<div class="col-md-10 col-md-offset-1">
							<?php if (have_posts()) : ?>
							<?php while (have_posts()) : the_post(); ?>
							<div class="post">
								<div class="post-thumbnail">
									<a target="_blank" href="<?php the_permalink(); ?>">
										<?php echo get_the_post_thumbnail( $post_id, 'full', array( 'class' =>'thumnail') ); ?>
									</a>
								</div>			
								<div class="post-title">
									<a target="_blank" href="<?php the_permalink(); ?>"><h2><?php the_title(); ?></h2></a>
								</div>
								<div class="post-body">
									  <?php the_content(); ?>
								</div>
							</div>
							<?php endwhile;?>
							<?php endif; ?>
						</div>
					</div>	
				</section>
			</div>	
		</div>
	<?php wp_footer(); ?>
</body>
</html>