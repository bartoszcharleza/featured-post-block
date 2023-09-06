<?php

function relatedPostsHTML($id) {

  // Get all posts that feature this professor
  $postsAboutThisProf = new WP_Query(array(
    'posts_per_page' => -1,  // fetch all posts
    'post_type' => 'post',   // specify the post type as 'post'
    'meta_query' => array(   // filter by meta field
      array(
        'key' => 'featuredprofessor',  // the meta key to look for
        'compare' => '=',              // comparison operator
        'value' => $id                 // meta value (professor ID)
      )
    )
  ));

  ob_start();

  // Check if there are posts featuring this professor
  if ($postsAboutThisProf->found_posts) { ?>
    <p><?php the_title(); ?> is mentioned in the following posts:</p>
    <ul>
      <?php
      while($postsAboutThisProf->have_posts()) {
        $postsAboutThisProf->the_post(); ?>
        <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
      <?php }
      ?>
    </ul>
  <?php }

  // Reset post data
  wp_reset_postdata();

  return ob_get_clean();
}
