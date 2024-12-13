import os
import json
import csv

# Define base directories for test and production environments
test_dir = 'test_results/'
prod_dir = 'prod_results/'

# Set the base directory (can be switched to prod_dir when needed)
# base_directory = test_dir  # Change to prod_dir for production
base_directory = prod_dir

# Define directories for each version
v1_dir = os.path.join('v1', base_directory)
v2_dir = os.path.join('v2', base_directory)

# Define the CSV output file
output_csv = 'raw_site_data.csv'

# Function to parse a single log file and extract necessary information
def parse_log_file(file_path, version):
    with open(file_path, 'r') as file:
        data = json.load(file)
    
    session_id = data[0]['sessionToken']  # All tasks have the same sessionToken
    task_info = {}
    total_score = 0

    for task in data:
        task_number = task['taskNumber']
        task_category = task['taskCategory']
        task_duration = task['taskDuration']
        task_completed = task['completed']
        task_correct = task['correct']

        # Add task info into the dictionary
        task_info[f'Task {task_number} Category'] = task_category
        task_info[f'Task {task_number} Correct'] = 'Yes' if task_correct else 'No'
        task_info[f'Task {task_number} Duration (s)'] = round(task_duration, 2)
        task_info[f'Task {task_number} Completed'] = 'Yes' if task_completed else 'No'

        if task_correct:
            total_score += 1
    
    # Add total score and version information
    task_info['Total Score'] = total_score
    task_info['Session ID'] = session_id
    task_info['Version'] = version  # Add version information

    return task_info

# Collect all JSON files in both directories
log_files_v1 = [(f, 'v1') for f in os.listdir(v1_dir) if f.endswith('.json')]
log_files_v2 = [(f, 'v2') for f in os.listdir(v2_dir) if f.endswith('.json')]

# Combine log files from both versions
log_files = log_files_v1 + log_files_v2

# Initialize the list of fieldnames for the CSV
fieldnames = ['Session ID', 'Version']
max_tasks = 0

# First pass to collect all possible fieldnames (tasks could vary in number)
for log_file, version in log_files:
    directory = v1_dir if version == 'v1' else v2_dir
    file_path = os.path.join(directory, log_file)
    data = parse_log_file(file_path, version)
    num_tasks = len([key for key in data.keys() if 'Task' in key and 'Correct' in key])
    max_tasks = max(max_tasks, num_tasks)

# Adjust fieldnames for the maximum number of tasks found
for task_num in range(1, max_tasks + 1):
    fieldnames.extend([f'Task {task_num} Category', f'Task {task_num} Correct', f'Task {task_num} Duration (s)', f'Task {task_num} Completed'])

fieldnames.append('Total Score')

# Write the CSV file
with open(output_csv, 'w', newline='') as csvfile:
    writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
    writer.writeheader()

    # Parse each log file and write data to the CSV
    for log_file, version in log_files:
        directory = v1_dir if version == 'v1' else v2_dir
        file_path = os.path.join(directory, log_file)
        row_data = parse_log_file(file_path, version)
        writer.writerow(row_data)

print(f"CSV file '{output_csv}' has been created successfully with version information.")
