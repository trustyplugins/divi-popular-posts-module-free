/* global tp_analytics */
import React, { Component } from 'react';
import './style.css';
import CustomLayout1 from './Layout1'; // Import the custom layout component
import CustomLayout2 from './Layout2';
class TpPopularPosts extends Component {
  static slug = 'tp_popular_posts';

  /**
   * All component inline styling.
   *
   * @since 1.0.0
   *
   * @param {Object} props      Module attribute names and values.
   * @param {Object} moduleInfo Module info.
   *
   * @return array
   */
  static css(props, moduleInfo) {
    // console.log(props.image_height,props.post_layout);
    //const utils         = window.ET_Builder.API.Utils;
    const additionalCss = [];

    // Process text-align value into style
    if (props.post_background_color) {
      additionalCss.push([{
        selector: '%%order_class%% article.tp-divi-popular-post',
        declaration: `background-color: ${props.post_background_color};`,
      }]);
    }
    if (props.post_padding) {
      const padding_part = props.post_padding.split('|');
      const padding_top = (padding_part[0] !== "") ? padding_part[0] : "0px";
      const padding_right = (padding_part[1] !== "") ? padding_part[1] : "0px";
      const padding_bottom = (padding_part[2] !== "") ? padding_part[2] : "0px";
      const padding_left = (padding_part[3] !== "") ? padding_part[3] : "0px";
      additionalCss.push([{
        selector: '%%order_class%% article.tp-divi-popular-post',
        declaration: `padding: ${padding_top} ${padding_right} ${padding_bottom} ${padding_left};`,
      }]);
    }
    if (props.post_margin) {
      const margin_part = props.post_margin.split('|');
      const margin_top = (margin_part[0] !== "") ? margin_part[0] : "0px";
      const margin_right = (margin_part[1] !== "") ? margin_part[1] : "0px";
      const margin_bottom = (margin_part[2] !== "") ? margin_part[2] : "0px";
      const margin_left = (margin_part[3] !== "") ? margin_part[3] : "0px";
      additionalCss.push([{
        selector: '%%order_class%% article.tp-divi-popular-post',
        declaration: `margin: ${margin_top} ${margin_right} ${margin_bottom} ${margin_left};`,
      }]);
    }
    if (props.post_inner_padding) {
      const padding_part = props.post_inner_padding.split('|');
      const padding_top = (padding_part[0] !== "") ? padding_part[0] : "0px";
      const padding_right = (padding_part[1] !== "") ? padding_part[1] : "0px";
      const padding_bottom = (padding_part[2] !== "") ? padding_part[2] : "0px";
      const padding_left = (padding_part[3] !== "") ? padding_part[3] : "0px";
      additionalCss.push([{
        selector: '%%order_class%% article.tp-divi-popular-post.layout2 .tp-post-inner-container',
        declaration: `padding: ${padding_top} ${padding_right} ${padding_bottom} ${padding_left};`,
      }]);
    }
    if (props.image_width) {
      additionalCss.push([{
        selector: '%%order_class%% article.tp-divi-popular-post .tp-left-wrapper',
        declaration: `width: ${props.image_width};`,
      }]);
    }
    if (props.image_min_height) {
      additionalCss.push([{
        selector: '%%order_class%% article.tp-divi-popular-post .tp-left-wrapper',
        declaration: `min-height: ${props.image_min_height};`,
      }]);
    }
    if (props.image_height) {
      if (props.post_layout === 'layout1') {
        additionalCss.push([{
          selector: '%%order_class%% article.tp-divi-popular-post .tp-left-wrapper',
          declaration: `height: ${props.image_height};`,
        }]);
      }
      if (props.post_layout === 'layout2') {
        additionalCss.push([{
          selector: '%%order_class%% article.tp-divi-popular-post.layout2 .tp-post-thumb img',
          declaration: `height: ${props.image_height};`,
        }]);
      }
    }
    if (props.image_max_height) {
      if (props.post_layout === 'layout1') {
        additionalCss.push([{
          selector: '%%order_class%% article.tp-divi-popular-post .tp-left-wrapper',
          declaration: `max-height: ${props.image_max_height};`,
        }]);
      }
      if (props.post_layout === 'layout2') {
        additionalCss.push([{
          selector: '%%order_class%% article.tp-divi-popular-post.layout2 .tp-post-thumb img',
          declaration: `max-height: ${props.image_max_height};`,
        }]);
      }
    }
    return additionalCss;
  }


  constructor(props) {
    //console.log(props);
    super(props);
    this.state = {
      html: '', // Store fetched HTML here
      loading: true,
    };
    this._isMounted = false; // Add a flag to track mounting status
  }

  componentDidMount() {
    this._isMounted = true; // Set the flag to true when mounted
    this.fetchRenderedHTML();
  }
  componentWillUnmount() {
    this._isMounted = false; // Reset the flag when unmounted
  }
  
  fetchRenderedHTML = () => {
    //console.log(tp_analytics);
    const endpoint = `${tp_analytics.site_url}/wp-json/tp/v1/render/`;

    fetch(endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json', // Send attributes as JSON
      },
      body: JSON.stringify({
        attributes: this.props,
      }),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json(); // Parse response as JSON
      })
      .then((data) => {
       // console.log(data);
        if (this._isMounted) { // Check if the component is still mounted
          this.setState({ html: data, loading: false });
         // console.log(data);
        }
      })
      .catch((error) => {
        if (this._isMounted) {
          console.error('Error fetching rendered HTML:', error);
          this.setState({ loading: false });
        }
      });
  };

  render() {
    const { html, loading } = this.state;

    return (
      <div className="tp-popular-posts">
        {loading ? (
          <></>
        ) : (
          Array.isArray(html) ? (
            html.length !== 0 ? (
              this.props.post_layout === 'layout2' ? (
                <div className={`tp-layout-container ${this.props.column_layout} tablet-${this.props.column_layout_tablet} phone-${this.props.column_layout_phone}`}> {/* Conditional wrapper for layout2 */}
                  {html.map((item) => (
                    <React.Fragment key={item.id}>
                      <CustomLayout2
                        key={item.id}
                        props={this.props}
                        item={item}
                      />
                    </React.Fragment>
                  ))}
                </div>
              ) : (
                html.map((item) => (
                  <React.Fragment key={item.id}>
                    {this.props.post_layout === 'layout1' && (
                      <CustomLayout1
                        key={item.id}
                        props={this.props}
                        item={item}
                      />
                    )}
                  </React.Fragment>
                ))
              )
            ) : (
              <div>No view count for any post.</div> // Message for an empty array
            )
          ) : (
            <div>No view count for any post.</div> // Message for non-array data
          )
        )}
      </div>
    );
  }
  
}

export default TpPopularPosts;
