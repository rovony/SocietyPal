import plotly.graph_objects as go
import plotly.express as px
import pandas as pd
import numpy as np

# Define the flowchart data with very short labels to avoid truncation
nodes = [
    {"id": "start", "label": "Step 18<br>Setup", "type": "start", "x": 5, "y": 12},
    {"id": "deploy_type", "label": "First<br>Deploy?", "type": "decision", "x": 5, "y": 10.5},
    {"id": "framework", "label": "Detect<br>Framework", "type": "process", "x": 5, "y": 9},
    {"id": "classify", "label": "Classify<br>Assets", "type": "process", "x": 5, "y": 7.5},
    {"id": "app_assets", "label": "App Assets<br>flags/, css/", "type": "app_data", "x": 1.5, "y": 6},
    {"id": "user_data", "label": "User Data<br>uploads/", "type": "user_data", "x": 3.5, "y": 6},
    {"id": "demo_data", "label": "Demo Data<br>samples", "type": "demo_data", "x": 6.5, "y": 6},
    {"id": "runtime_data", "label": "Runtime<br>qrcodes/", "type": "runtime_data", "x": 8.5, "y": 6},
    {"id": "hosting_type", "label": "VPS or<br>Shared?", "type": "decision", "x": 5, "y": 4.5},
    {"id": "symlink_path", "label": "Create<br>Symlinks", "type": "process", "x": 2.5, "y": 3},
    {"id": "manual_path", "label": "Manual<br>Copy", "type": "process", "x": 7.5, "y": 3},
    {"id": "verify", "label": "Verify &<br>Check", "type": "process", "x": 5, "y": 1.5},
    {"id": "complete", "label": "Zero Data<br>Loss ✅", "type": "end", "x": 5, "y": 0}
]

connections = [
    {"from": "start", "to": "deploy_type"},
    {"from": "deploy_type", "to": "framework"},
    {"from": "framework", "to": "classify"},
    {"from": "classify", "to": "app_assets"},
    {"from": "classify", "to": "user_data"},
    {"from": "classify", "to": "demo_data"},
    {"from": "classify", "to": "runtime_data"},
    {"from": "app_assets", "to": "hosting_type"},
    {"from": "user_data", "to": "hosting_type"},
    {"from": "demo_data", "to": "hosting_type"},
    {"from": "runtime_data", "to": "hosting_type"},
    {"from": "hosting_type", "to": "symlink_path", "label": "VPS"},
    {"from": "hosting_type", "to": "manual_path", "label": "Shared"},
    {"from": "symlink_path", "to": "verify"},
    {"from": "manual_path", "to": "verify"},
    {"from": "verify", "to": "complete"}
]

# Create node lookup
node_dict = {node["id"]: node for node in nodes}

# Define colors for different node types
color_map = {
    "start": "#1FB8CD",
    "end": "#1FB8CD", 
    "decision": "#DB4545",
    "process": "#2E8B57",
    "app_data": "#5D878F",  # Blue for app assets
    "user_data": "#2E8B57",  # Green for user data  
    "demo_data": "#D2BA4C",  # Yellow for demo data
    "runtime_data": "#B4413C"  # Purple-ish for runtime data
}

# Create figure
fig = go.Figure()

# Add connections first (so they appear behind nodes)
for conn in connections:
    from_node = node_dict[conn["from"]]
    to_node = node_dict[conn["to"]]
    
    # Calculate arrow positions with offset for larger node size
    dx = to_node["x"] - from_node["x"]
    dy = to_node["y"] - from_node["y"]
    length = np.sqrt(dx**2 + dy**2)
    
    if length > 0:
        # Normalize direction
        dx_norm = dx / length
        dy_norm = dy / length
        
        # Start and end points with larger node offset
        if from_node["type"] == "decision":
            start_offset = 0.7
        else:
            start_offset = 0.6
            
        if to_node["type"] == "decision":
            end_offset = 0.7
        else:
            end_offset = 0.6
            
        start_x = from_node["x"] + dx_norm * start_offset
        start_y = from_node["y"] + dy_norm * start_offset
        end_x = to_node["x"] - dx_norm * end_offset
        end_y = to_node["y"] - dy_norm * end_offset
        
        # Add arrow
        fig.add_annotation(
            x=end_x,
            y=end_y,
            ax=start_x,
            ay=start_y,
            arrowhead=2,
            arrowsize=2.5,
            arrowwidth=4,
            arrowcolor='#13343B',
            showarrow=True,
            text="",
            axref="x",
            ayref="y"
        )
    
    # Add connection labels with larger font
    if "label" in conn:
        mid_x = (from_node["x"] + to_node["x"]) / 2
        mid_y = (from_node["y"] + to_node["y"]) / 2 + 0.3
        
        fig.add_annotation(
            x=mid_x,
            y=mid_y,
            text=f"<b>{conn['label']}</b>",
            showarrow=False,
            font=dict(size=14, color='#13343B', family='Arial Bold'),
            bgcolor='white',
            bordercolor='#13343B',
            borderwidth=2,
            borderpad=4
        )

# Add nodes with different shapes and larger sizes
for node in nodes:
    if node["type"] == "decision":
        # Diamond shape using larger path
        size = 0.8
        x_coords = [node["x"], node["x"]+size, node["x"], node["x"]-size, node["x"]]
        y_coords = [node["y"]+size, node["y"], node["y"]-size, node["y"], node["y"]+size]
        
        fig.add_trace(go.Scatter(
            x=x_coords,
            y=y_coords,
            fill='toself',
            fillcolor=color_map[node["type"]],
            line=dict(color='white', width=4),
            mode='lines',
            showlegend=False,
            hoverinfo='none'
        ))
        
        # Add text for diamond with larger font
        fig.add_annotation(
            x=node["x"],
            y=node["y"],
            text=node["label"],
            showarrow=False,
            font=dict(size=12, color='white', family='Arial Bold'),
            align='center'
        )
        
    else:
        # Rectangle or circle shape with larger sizes
        if node["type"] in ["start", "end"]:
            symbol = "circle"
            size = 100
        else:
            symbol = "square"
            size = 120
            
        fig.add_trace(go.Scatter(
            x=[node["x"]],
            y=[node["y"]],
            mode='markers+text',
            marker=dict(
                size=size,
                color=color_map[node["type"]],
                symbol=symbol,
                line=dict(width=4, color='white')
            ),
            text=node["label"],
            textposition="middle center",
            textfont=dict(size=12, color='white', family='Arial Bold'),
            showlegend=False,
            hoverinfo='none'
        ))

# Add legend with better positioning
legend_y_start = 11.5
legend_items = [
    ("Start/End", "#1FB8CD"),
    ("Decision", "#DB4545"), 
    ("Process", "#2E8B57"),
    ("App Assets", "#5D878F"),
    ("User Data", "#2E8B57"),
    ("Demo Data", "#D2BA4C"),
    ("Runtime Data", "#B4413C")
]

for i, (label, color) in enumerate(legend_items):
    fig.add_trace(go.Scatter(
        x=[11.5],
        y=[legend_y_start - i * 0.9],
        mode='markers+text',
        marker=dict(size=25, color=color, symbol='square'),
        text=f"  {label}",
        textposition="middle right",
        textfont=dict(size=12, color='#13343B', family='Arial'),
        showlegend=False,
        hoverinfo='none'
    ))

# Update layout with more space
fig.update_layout(
    title="Step 18 Data Persistence Flow",
    xaxis=dict(
        range=[-1, 15],
        showgrid=False,
        showticklabels=False,
        zeroline=False,
        fixedrange=True
    ),
    yaxis=dict(
        range=[-1, 13],
        showgrid=False,
        showticklabels=False,
        zeroline=False,
        fixedrange=True
    ),
    plot_bgcolor='white',
    showlegend=False,
    margin=dict(l=50, r=50, t=80, b=50)
)

# Add title annotation for legend
fig.add_annotation(
    x=11.5,
    y=12.5,
    text="<b>Legend</b>",
    showarrow=False,
    font=dict(size=16, color='#13343B', family='Arial Bold'),
    align='left'
)

# Save the chart with higher resolution
fig.write_image("step18_flowchart.png", width=1600, height=1200)