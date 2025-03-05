/* global tp_analytics */
import React, { Component, createRef } from "react";
import * as d3 from "d3";
import './style.css';
import LayoutCharts from './LayoutCharts'

class TpPopularPostCharts extends Component {
  static slug = 'tp_popular_post_charts';
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
    this.chartRef = createRef();
    this.chartRef2 = createRef();
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

    const endpoint = `${tp_analytics.site_url}/wp-json/tp/v1/render-charts/`;
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
        //console.log(data);
        if (this._isMounted) { // Check if the component is still mounted
          this.setState({ html: data, loading: false });
          if (this.props.charts_type === 'layout1') {
            this.renderChart();
          }
          if (this.props.charts_type === 'layout2') {
            let formattedData = '';
            if (data) {
              formattedData = data.map((item) => ({
                postName: item.title,
                id: parseInt(item.id),
                views: item.meta_views,
              }));
            }
            this.renderChart2(formattedData);
          }
        }
      })
      .catch((error) => {
        if (this._isMounted) {
          console.error('Error fetching rendered HTML:', error);
          this.setState({ loading: false });
        }
      });
  };

  renderChart2 = (data) => {
    //console.log(data);
    if (!Array.isArray(data)) {
      console.warn("Invalid data received:", data);
      data = [];
    }
    if (!data || data.length === 0) {
      data = [];
  } else {
      data = data.slice(0, this.props.chart_posts); // Get only the top 10
  }
    var win_width = document.documentElement.clientWidth;
    if (win_width > 768) {
      var width_var = parseInt(this.props.chart_width, 10);
    }
    if (win_width > 500 && win_width <= 768) {
      if (this.props.chart_width_tablet !== '') {
        width_var = parseInt(this.props.chart_width_tablet, 10);
      }
      else {
        width_var = parseInt(this.props.chart_width, 10);
      }
    }
    if (win_width <= 500) {
      if (this.props.chart_width_phone !== '') {
        width_var = parseInt(this.props.chart_width_phone, 10);
      }
      else {
        width_var = parseInt(this.props.chart_width, 10);
      }
    }
    // console.log(win_width,width_var);
    const width = width_var;
    const height = 300;
    const margin = { top: 20, right: 30, bottom: 80, left: 50 };

    // Clear any existing chart
    d3.select(this.chartRef2.current).select("svg").remove();

    // Create the SVG canvas
    const svg = d3
      .select(this.chartRef2.current)
      .append("svg")
      .attr("width", width)
      .attr("height", height);
//console.log(data);

const xScale = d3
  .scaleBand()
  .domain(data.map(d => d.postName))
  .range([margin.left, width - margin.right])
  .padding(0.3);
    const yScale = d3
      .scaleLinear()
      .domain([0, d3.max(data, (d) => Number(d.views))])
      .nice()
      .range([height - margin.bottom, margin.top]);

    // Add axes
    svg
      .append("g")
      .attr("transform", `translate(0, ${height - margin.bottom})`)
      .call(d3.axisBottom(xScale))
      .selectAll("text")
      .attr("transform", "rotate(-45)") // Rotate labels for readability
      .style("text-anchor", "end");

    svg
      .append("g")
      .attr("transform", `translate(${margin.left}, 0)`)
      .call(d3.axisLeft(yScale));

    // Add bars
    const bars = svg
      .selectAll(".bar")
      .data(data)
      .join("rect")
      .attr("class", "bar")
      .attr("x", (d) => xScale(d.postName))
      .attr("y", () => height - margin.bottom) // Start from the bottom
      .attr("width", xScale.bandwidth())
      .attr("height", 0) // Start with no height
      .attr("fill", "steelblue")
      .attr("rx", 2.5); // Adds rounded corner
    // Apply animation only on the first load
    bars
  .transition() // Add a transition for the first load
  .duration(800)
  .attr("y", (d) => yScale(d.views)) // Move to the final y position
  .attr("height", (d) => height - margin.bottom - yScale(d.views)) // Grow the height upwards
  .attr("fill", (d, i) => d3.schemeCategory10[i % 10]); // Assign random color from D3 scheme



    // Add tooltips
    const tooltip = d3
      .select(this.chartRef2.current)
      .append("div")
      .style("position", "absolute")
      .style("background", "white")
      .style("border", "1px solid #ccc")
      .style("padding", "8px")
      .style("border-radius", "4px")
      .style("visibility", "hidden");

    svg
      .selectAll(".bar")
      .on("mouseover", (event, d) => {
        tooltip
          .html(
            `<strong>${d.postName}</strong><br>ID: ${d.id}<br>Views: ${d.views}`
          )
          .style("visibility", "visible")
          .style("left", `${event.pageX - 250}px`)
          .style("top", `${event.pageY - 0}px`);
      })
      .on("mousemove", (event) => {
        tooltip
          .style("left", `${event.pageX - 250}px`)
          .style("top", `${event.pageY - 0}px`);
      })
      .on("mouseout", () => {
        tooltip.style("visibility", "hidden");
      });

  }

  renderChart = () => {
    const { html } = this.state;
    const { chart_width, chart_width_tablet, chart_width_phone, chart_posts } = this.props;
    
    if (!html.length) return;
  
    const win_width = document.documentElement.clientWidth;
    let width_var = parseInt(chart_width, 10);
  
    if (win_width <= 768) {
      width_var = chart_width_tablet ? parseInt(chart_width_tablet, 10) : width_var;
    }
    if (win_width <= 500) {
      width_var = chart_width_phone ? parseInt(chart_width_phone, 10) : width_var;
    }
  
    const width = width_var || 400, // Default width if undefined
          height = 400,
          radius = Math.min(width, height) / 2;
  
    const svgContainer = d3.select(this.chartRef.current);
    svgContainer.selectAll("*").remove(); // Clear previous chart
  
    const svg = svgContainer
      .append("svg")
      .attr("width", width)
      .attr("height", height)
      .append("g")
      .attr("transform", `translate(${width / 2},${height / 2})`);
  
    const color = d3.scaleOrdinal(d3.schemeCategory10);
  
    const pie = d3.pie().value(d => d.percentage);
    const data = pie(html.slice(0, chart_posts)); // Limit to chart_posts count
  
    const arc = d3.arc().innerRadius(0).outerRadius(radius);
  
    // Create tooltip element (only once)
    let tooltip = d3.select(".tooltip");
    if (tooltip.empty()) {
      tooltip = d3.select("body").append("div")
        .attr("class", "tooltip")
        .style("position", "absolute")
        .style("visibility", "hidden")
        .style("background-color", "#fff")
        .style("border", "1px solid #ccc")
        .style("border-radius", "5px")
        .style("padding", "5px")
        .style("box-shadow", "0px 0px 5px rgba(0,0,0,0.2)");
    }
  
    // Draw Pie Chart Slices
    svg.selectAll("path")
      .data(data)
      .enter()
      .append("path")
      .attr("d", arc)
      .attr("fill", (d, i) => color(i))
      .attr("stroke", "#fff")
      .style("stroke-width", "2px")
      .on("mouseover", (event, d) => {
        tooltip.style("visibility", "visible")
          .html(`<strong>Post Title:</strong> ${d.data.title}<br><strong>Percentage:</strong> ${d.data.percentage}%`);
      })
      .on("mousemove", (event) => {
        tooltip.style("top", (event.pageY + 10) + "px")
          .style("left", (event.pageX + 10) + "px");
      })
      .on("mouseout", () => {
        tooltip.style("visibility", "hidden");
      });
  
    // Add percentage labels
    svg.selectAll("text")
      .data(data)
      .enter()
      .append("text")
      .attr("transform", d => `translate(${arc.centroid(d)})`)
      .attr("text-anchor", "middle")
      .attr("font-size", "12px")
      .attr("fill", "#fff")
      .text(d => `${d.data.percentage}%`);
  };
  




  render() {
    const { html, loading } = this.state;
    //console.log(this.props.show_posts);
    return (
      <div className={`tp-chart-container-front ${this.props.charts_type} ${this.props.charts_column || ''}`}>
        <div ref={this.chartRef} className="chart-container-layout1"></div>
        <div ref={this.chartRef2} className="chart-container-layout2"></div>
        {loading ? (
          <></>
        ) : (
          Array.isArray(html) && this.props.show_posts === 'on' ? (
            html.length !== 0 ? (

              <div className={`chart-container-posts`}>
              {html.slice(0, this.props.posts_number).map((item) => (
                  <React.Fragment key={item.id}>
                    <LayoutCharts
                      key={item.id}
                      props={this.props}
                      item={item}
                    />
                  </React.Fragment>
                ))}
              </div>

            ) : (
              <div>No view count for any post.</div> // Message for an empty array
            )
          ) : (
            <></> // Message for non-array data
          )
        )}
      </div>
    );
  }

}

export default TpPopularPostCharts;
