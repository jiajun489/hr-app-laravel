from flask import Flask, request, jsonify
from datetime import datetime
import os

app = Flask(__name__)

API_KEY = os.getenv('API_KEY', 'hr-analytics-secret-key')

def authenticate():
    return request.headers.get('X-API-KEY') == API_KEY

@app.route('/analyze', methods=['POST'])
def analyze():
    if not authenticate():
        return jsonify({'error': 'Unauthorized'}), 401
    
    data = request.get_json()
    
    users = data.get('users', [])
    tasks = data.get('tasks', [])
    presences = data.get('presences', [])
    
    # Employee Workload Analysis
    workload = []
    if users and tasks:
        workload_counts = {}
        for task in tasks:
            if task['status'] in ['in_progress', 'pending']:
                assigned_to = task['assigned_to']
                workload_counts[assigned_to] = workload_counts.get(assigned_to, 0) + 1
        
        for user in users:
            if user['id'] in workload_counts:
                workload.append({
                    'employee_name': user['name'],
                    'active_tasks': workload_counts[user['id']]
                })
    
    # Task Bottleneck Analysis
    bottlenecks = {'pending': 0, 'in_progress': 0, 'completed': 0}
    if tasks:
        for task in tasks:
            status = task['status']
            if status in bottlenecks:
                bottlenecks[status] += 1
    
    # Punctuality & Productivity Correlation
    productivity = []
    if users and tasks and presences:
        for user in users:
            user_id = user['id']
            
            # Calculate lateness rate
            user_presences = [p for p in presences if p['employee_id'] == user_id]
            if user_presences:
                late_count = sum(1 for p in user_presences if p['status'] == 'late')
                lateness_rate = (late_count / len(user_presences) * 100) if user_presences else 0
            else:
                lateness_rate = 0
            
            # Calculate completion rate
            user_tasks = [t for t in tasks if t['assigned_to'] == user_id]
            if user_tasks:
                completed_count = sum(1 for t in user_tasks if t['status'] == 'completed')
                completion_rate = (completed_count / len(user_tasks) * 100) if user_tasks else 0
            else:
                completion_rate = 0
            
            productivity.append({
                'employee_name': user['name'],
                'lateness_rate': round(lateness_rate, 1),
                'completion_rate': round(completion_rate, 1)
            })
    
    # Overdue Task Analysis
    overdue = []
    if users and tasks:
        today = datetime.now().date()
        overdue_counts = {}
        
        for task in tasks:
            if task['status'] != 'completed':
                try:
                    due_date = datetime.fromisoformat(task['due_date'].replace('Z', '+00:00')).date()
                    if due_date < today:
                        assigned_to = task['assigned_to']
                        overdue_counts[assigned_to] = overdue_counts.get(assigned_to, 0) + 1
                except:
                    continue
        
        for user in users:
            if user['id'] in overdue_counts:
                overdue.append({
                    'employee_name': user['name'],
                    'overdue_count': overdue_counts[user['id']]
                })
    
    return jsonify({
        'workload': sorted(workload, key=lambda x: x['active_tasks'], reverse=True),
        'bottlenecks': bottlenecks,
        'productivity': productivity,
        'overdue': sorted(overdue, key=lambda x: x['overdue_count'], reverse=True)
    })

@app.route('/health', methods=['GET'])
def health():
    return jsonify({'status': 'healthy'})

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5001)