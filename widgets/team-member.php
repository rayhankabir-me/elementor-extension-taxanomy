<?php


/*retrieving taxanomies names/slug */

function team_members_taxanomies()
{

    $term_id = 'member_categories';
    $taxanomies = get_terms($term_id);
    if (!empty($taxanomies)) {
        foreach ($taxanomies as $taxanomoy) {

            $taxanomy_info = get_term($taxanomoy, $term_id);

            $taxanomy_array[$taxanomy_info->slug] = $taxanomy_info->name;
        }
    }

    return $taxanomy_array;
}

/**
 * Elementor oEmbed Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Moyerchor_Team_Member extends \Elementor\Widget_Base
{

    /**
     * Get widget name.
     *
     * Retrieve oEmbed widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'team-member';
    }

    /**
     * Get widget title.
     *
     * Retrieve oEmbed widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return __('Team Member', 'moyerchor');
    }

    /**
     * Get widget icon.
     *
     * Retrieve oEmbed widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'fas fa-code';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the oEmbed widget belongs to.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories()
    {
        return ['moyerchor'];
    }

    /**
     * Register oEmbed widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function _register_controls()
    {

        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'moyerchor'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );


        $this->add_control(
            'post-count',
            [
                'label' => __('Post Count', 'moyerchor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => '-1',
            ]
        );

        $this->add_control(
            'select-category',
            [
                'label' => __('Select Category', 'moyerchor'),
                'multiple' => true,
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => team_members_taxanomies(),
            ]
        );
        
        

        $this->end_controls_section();


        $this->start_controls_section(
            'style',
            [
                'label' => __('Style', 'plugin-name'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'tilte_typography',
                'label' => __('Title Typography', 'moyerchor'),
                'scheme' => Elementor\Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .team-heading a',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'desc_typography',
                'label' => __('Desc Typography', 'moyerchor'),
                'scheme' => Elementor\Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .team-description p',
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'status_typography',
                'label' => __('Desc Typography', 'moyerchor'),
                'scheme' => Elementor\Scheme_Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .team-status h4',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render oEmbed widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render()
    {



        $settings = $this->get_settings_for_display();

        $post_count = $settings['post-count'];



        $cat_item = implode(',', $settings['select-category']);


        $team_posts = new WP_Query(array(
            'post_type' => 'team-members',
            'post_status' => 'publish',
            'posts_per_page'    => $post_count,
    'tax_query' => array(
        array(
            'taxonomy' => 'member_categories',
            'field'    => 'member_categories',
            'terms'    => '$cat_item',
        ),
    ),
            

        ));



        echo '<div class="team-wrapper">';

        if ($team_posts->have_posts()) :
            while ($team_posts->have_posts()) : $team_posts->the_post();

                $team_member_image_url = get_the_post_thumbnail_url(get_the_ID(), 'large');



?>




                <div class="team-card">
                    <div class="team-image">
                        <a href="<?php the_permalink(); ?>"><img src="<?php echo $team_member_image_url; ?>" alt=""></a>
                    </div>
                    <div class="team-heading">
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    </div>
                    <div class="team-description">
                        <?php the_excerpt(); ?>
                    </div>




                    <div class="team-status">
                        <h4><?php echo the_field('status'); ?></h4>
                    </div>
                    <div class="team-icons">
                        <a href=""><i aria-hidden="true" class="fab fa-facebook-f"></i></a>
                        <a href=""><i aria-hidden="true" class="fab fa-twitter"></i></a>
                        <a href=""><i aria-hidden="true" class="fab fa-linkedin-in"></i></a>
                        <a href=""><i aria-hidden="true" class="fab fa-instagram"></i></a>
                    </div>
                </div>



<?php



            endwhile;
            echo "</div>";
        else :
            _e('Sorry, no posts matched your criteria.', 'moyerchor');
        endif;
    }
}