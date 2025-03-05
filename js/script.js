document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.tp-chart-container-front').forEach(function (module) {
        const chartData = JSON.parse(module.getAttribute('data-chart')); // Retrieve chart data from data-attribute
       // console.log(chartData);
        if(chartData['data']['chart_layout']==='layout1') {
        if (typeof chartData !== 'undefined') {
            var win_width = jQuery(window).width();
            var width;  // Declare width variable here
            
            // Determine width based on window size
            if (win_width > 768) {
                width = parseInt(chartData['data'].chart_width, 10);
            } else if (win_width > 500 && win_width <= 768) {
                width = chartData['data'].chart_width_tablet !== '' 
                        ? parseInt(chartData['data'].chart_width_tablet, 10) 
                        : parseInt(chartData['data'].chart_width, 10);
            } else if (win_width <= 500) {
                width = chartData['data'].chart_width_phone !== '' 
                        ? parseInt(chartData['data'].chart_width_phone, 10) 
                        : parseInt(chartData['data'].chart_width, 10);
            }

          //  console.log(width);
            const height = 400;
            const radius = Math.min(width, height) / 2;

            const svgContainer = module.querySelector('.chart-container-layout1'); // Target specific chart instance
            svgContainer.innerHTML = ''; // Clear previous chart content
            
            const svg = d3.select(svgContainer)
                .append("svg")
                .attr("width", width)
                .attr("height", height)
                .append("g")
                .attr("transform", `translate(${width / 2},${height / 2})`);

            const color = d3.scaleOrdinal(d3.schemeCategory10);
            const pie = d3.pie().value(d => d.percentage);
            const data = pie(chartData['loop']);

            const arc = d3.arc().innerRadius(0).outerRadius(radius);

            // Create the chart slices
            const paths = svg
                .selectAll("path")
                .data(data)
                .enter()
                .append("path")
                .attr("d", arc)
                .attr("fill", (d, i) => color(i))
                .attr("stroke", "#fff")
                .style("stroke-width", "2px");

            // Add percentage labels inside slices
            svg
                .selectAll("text")
                .data(data)
                .enter()
                .append("text")
                .attr("transform", (d) => `translate(${arc.centroid(d)})`)
                .attr("text-anchor", "middle")
                .attr("font-size", "12px")
                .attr("fill", "#fff")
                .text((d) => `${d.data.percentage}%`);

            // Tooltip functionality
            const tooltip = d3.select(svgContainer)
                .append("div")
                .attr("class", "tooltip")
                .style("position", "absolute")
                .style("visibility", "hidden")
                .style("background-color", "#fff")
                .style("border", "1px solid #ccc")
                .style("border-radius", "5px")
                .style("padding", "5px")
                .style("box-shadow", "0px 0px 5px rgba(0,0,0,0.2)");

            // Bind tooltip to chart slices
            paths
                .on("mouseover", function (event, d) {
                    tooltip.style("visibility", "visible")
                        .html(`<strong>Post Title:</strong> ${d.data.title}<br><strong>Percentage:</strong> ${d.data.percentage}%`);
                })
                .on("mousemove", function (event) {
                    tooltip.style("top", (event.layerY + 25) + "px")
                        .style("left", (event.layerX + 15) + "px");
                })
                .on("mouseout", function () {
                    tooltip.style("visibility", "hidden");
                });
        }
    }
    if(chartData['data']['chart_layout']==='layout2') {

        var data = chartData['loop'];
    
        var win_width = jQuery(window).width();
        var width;
    
        // Determine width based on window size
        if (win_width > 768) {
            width = parseInt(chartData['data'].chart_width, 10);
        } else if (win_width > 500 && win_width <= 768) {
            width = chartData['data'].chart_width_tablet !== '' 
                    ? parseInt(chartData['data'].chart_width_tablet, 10) 
                    : parseInt(chartData['data'].chart_width, 10);
        } else if (win_width <= 500) {
            width = chartData['data'].chart_width_phone !== '' 
                    ? parseInt(chartData['data'].chart_width_phone, 10) 
                    : parseInt(chartData['data'].chart_width, 10);
        }
    
        const height = 300;
        const margin = { top: 20, right: 30, bottom: 80, left: 50 };
        const svgContainer = module.querySelector('.chart-container-layout2');
        svgContainer.innerHTML = ''; // Clear previous chart content
    
        // Create the SVG canvas
        const svg = d3
            .select(svgContainer)
            .append("svg")
            .attr("width", width)
            .attr("height", height);
    
        // Set up scales
        const xScale = d3
            .scaleBand()
            .domain(data.map((d) => d.title)) // Use post names for the x-axis
            .range([margin.left, width - margin.right])
            .padding(0.3);
    
        const yScale = d3
            .scaleLinear()
            .domain([0, d3.max(data, (d) => Number(d.meta_views))])
            .nice()
            .range([height - margin.bottom, margin.top]);
    
        // Add axes
        svg.append("g")
            .attr("transform", `translate(0, ${height - margin.bottom})`)
            .call(d3.axisBottom(xScale))
            .selectAll("text")
            .attr("transform", "rotate(-45)")
            .style("text-anchor", "end");
    
        svg.append("g")
            .attr("transform", `translate(${margin.left}, 0)`)
            .call(d3.axisLeft(yScale));
    
        // Add bars
        const bars = svg
            .selectAll(".bar")
            .data(data)
            .join("rect")
            .attr("class", "bar")
            .attr("x", (d) => xScale(d.title))
            .attr("y", () => height - margin.bottom)
            .attr("width", xScale.bandwidth())
            .attr("height", 0)
            .attr("fill", "steelblue")
            .attr("rx", 2.5); // Rounded corners
    
        // Apply animation
        bars.transition()
            .duration(800)
            .attr("y", (d) => yScale(d.meta_views))
            .attr("height", (d) => height - margin.bottom - yScale(d.meta_views))
            .attr("fill", (d, i) => d3.schemeCategory10[i % 10]);
    
        // **Tooltip Implementation**
        const tooltip = d3.select(svgContainer)
            .append("div")
            .style("position", "absolute")
            .style("background", "#fff")
            .style("border", "1px solid #ccc")
            .style("padding", "8px")
            .style("border-radius", "4px")
            .style("visibility", "hidden")
            .style("box-shadow", "2px 2px 10px rgba(0, 0, 0, 0.1)")
            .style("pointer-events", "none"); // Prevent flickering
    
        bars.on("mouseover", (event, d) => {
                tooltip.html(`<strong>${d.title}</strong><br>Views: ${d.meta_views}`)
                    .style("visibility", "visible")
                    .style("left", `${event.layerX - 50}px`)
                    .style("top", `${event.layerY - 50}px`);
            })
            .on("mousemove", (event) => {
                tooltip.style("left", `${event.layerX - 20}px`)
                    .style("top", `${event.layerY + 20}px`);
            })
            .on("mouseout", () => {
                tooltip.style("visibility", "hidden");
            });
    
    }
    


});
});
