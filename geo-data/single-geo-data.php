<?php
 /*Template Name: New Template
 */
 
get_header(); ?>
<div id="primary">
    <div id="content" role="main">
    <?php
    $mypost = array( 'post_type' => 'geo_data', );
    $loop = new WP_Query( $mypost );
    ?>
    <?php while ( $loop->have_posts() ) : $loop->the_post();?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
 
                <!-- Display featured image in right-aligned floating div -->
                <div style="float: right; margin: 10px">
                    <?php the_post_thumbnail( array( 100, 100 ) ); ?>
                </div>
 
                <!-- Display Title and Personalizer Tags -->
                <strong>Title: </strong><?php the_title(); ?><br />				
                <strong>Tags: </strong>
                <?php echo esc_html( get_post_meta( get_the_ID(), 'tagsId', true ) ); ?>
                <br /> 
                <!-- Display Expiration Date and Dealer Inspire Author -->
                <strong>Expiration Date: </strong>
                <?php echo esc_html( get_post_meta( get_the_ID(), 'exp_date', true ) );  ?><br />
				<strong>Dealer Inspire Author: </strong>
                <?php echo esc_html( get_post_meta( get_the_ID(), 'inspire_author', true ) );  ?><br />
            </header>
 
            <!-- Display geo_data contents -->
            <div class="entry-content"><?php the_content(); ?></div>
        </article>
 
    <?php endwhile; ?>
    </div>
</div>
<?php wp_reset_query(); ?>
<?php get_footer(); ?>