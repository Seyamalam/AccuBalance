from faker import Faker
import mysql.connector
from datetime import datetime, timedelta
import random
import json

# Initialize Faker
fake = Faker()

# Database connection
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'finance_manager'
}

def connect_to_database():
    return mysql.connector.connect(**db_config)

def generate_accounts(cursor, user_id, num_accounts=4):
    account_types = ['checking', 'savings', 'credit', 'investment']
    accounts = []
    
    for i in range(num_accounts):
        account = {
            'user_id': user_id,
            'account_name': f"{fake.company()} {account_types[i]}",
            'account_type': account_types[i],
            'balance': round(random.uniform(1000, 50000), 2),
            'currency': 'USD'
        }
        
        cursor.execute("""
            INSERT INTO accounts (user_id, account_name, account_type, balance, currency)
            VALUES (%(user_id)s, %(account_name)s, %(account_type)s, %(balance)s, %(currency)s)
        """, account)
        
        accounts.append(cursor.lastrowid)
    
    return accounts

def generate_budget_categories(cursor, user_id):
    categories = [
        ('Groceries', 'expense', '🛒', '#FF5733'),
        ('Transportation', 'expense', '🚗', '#33FF57'),
        ('Entertainment', 'expense', '🎬', '#3357FF'),
        ('Salary', 'income', '💰', '#FFD700'),
        ('Freelance', 'income', '💻', '#9370DB'),
        ('Investments', 'income', '📈', '#20B2AA')
    ]
    
    category_ids = []
    for name, type_, icon, color in categories:
        cursor.execute("""
            INSERT INTO budget_categories (user_id, name, type, icon, color)
            VALUES (%s, %s, %s, %s, %s)
        """, (user_id, name, type_, icon, color))
        category_ids.append(cursor.lastrowid)
    
    return category_ids

def generate_transactions(cursor, account_ids, start_date, num_transactions=100):
    transaction_types = ['income', 'expense', 'transfer']
    categories = ['Food', 'Transport', 'Utilities', 'Entertainment', 'Salary', 'Shopping', 'Healthcare', 'Rent']
    
    transactions = []
    for _ in range(num_transactions):
        transaction = {
            'account_id': random.choice(account_ids),
            'type': random.choice(transaction_types),
            'category': random.choice(categories),
            'amount': round(random.uniform(10, 1000), 2),
            'description': fake.sentence(),
            'transaction_date': fake.date_between(start_date=start_date),
        }
        
        cursor.execute("""
            INSERT INTO transactions (account_id, type, category, amount, description, transaction_date)
            VALUES (%(account_id)s, %(type)s, %(category)s, %(amount)s, %(description)s, %(transaction_date)s)
        """, transaction)
        transactions.append(cursor.lastrowid)
    
    return transactions

def generate_transaction_tags(cursor, user_id):
    tags = [
        ('Essential', '#FF0000'),
        ('Discretionary', '#00FF00'),
        ('Investment', '#0000FF'),
        ('Emergency', '#FFA500'),
        ('Business', '#800080')
    ]
    
    tag_ids = []
    for name, color in tags:
        cursor.execute("""
            INSERT INTO transaction_tags (user_id, name, color)
            VALUES (%s, %s, %s)
        """, (user_id, name, color))
        tag_ids.append(cursor.lastrowid)
    
    return tag_ids

def generate_transaction_tag_relations(cursor, transaction_ids, tag_ids):
    for transaction_id in transaction_ids:
        # Assign 1-3 random tags to each transaction
        for tag_id in random.sample(tag_ids, random.randint(1, 3)):
            cursor.execute("""
                INSERT INTO transaction_tag_relations (transaction_id, tag_id)
                VALUES (%s, %s)
            """, (transaction_id, tag_id))

def generate_goal_categories(cursor, user_id):
    categories = [
        ('Retirement', '👴', '#FFD700'),
        ('Education', '🎓', '#4169E1'),
        ('Home', '🏠', '#32CD32'),
        ('Travel', '✈️', '#FF69B4'),
        ('Emergency Fund', '🏦', '#8B4513')
    ]
    
    category_ids = []
    for name, icon, color in categories:
        cursor.execute("""
            INSERT INTO goal_categories (user_id, name, icon, color)
            VALUES (%s, %s, %s, %s)
        """, (user_id, name, icon, color))
        category_ids.append(cursor.lastrowid)
    
    return category_ids

def generate_goals(cursor, user_id, category_ids, num_goals=5):
    goal_ids = []
    for i in range(num_goals):
        target_amount = round(random.uniform(5000, 20000), 2)
        goal = {
            'user_id': user_id,
            'category_id': random.choice(category_ids),
            'goal_name': f"{fake.bs()} Fund",
            'target_amount': target_amount,
            'current_amount': round(random.uniform(0, target_amount), 2),
            'deadline': fake.date_between(start_date='+1y', end_date='+3y'),
            'priority': random.choice(['low', 'medium', 'high']),
            'auto_save': random.choice([True, False]),
            'auto_save_amount': round(random.uniform(50, 500), 2),
            'auto_save_frequency': random.choice(['daily', 'weekly', 'monthly']),
            'reminder_frequency': random.choice([7, 14, 30])
        }
        
        cursor.execute("""
            INSERT INTO goals (user_id, category_id, goal_name, target_amount, current_amount, 
                             deadline, priority, auto_save, auto_save_amount, auto_save_frequency,
                             reminder_frequency)
            VALUES (%(user_id)s, %(category_id)s, %(goal_name)s, %(target_amount)s, 
                    %(current_amount)s, %(deadline)s, %(priority)s, %(auto_save)s,
                    %(auto_save_amount)s, %(auto_save_frequency)s, %(reminder_frequency)s)
        """, goal)
        goal_ids.append(cursor.lastrowid)
    
    return goal_ids

def generate_goal_milestones(cursor, goal_ids):
    for goal_id in goal_ids:
        num_milestones = random.randint(2, 4)
        for i in range(num_milestones):
            milestone = {
                'goal_id': goal_id,
                'title': f"Milestone {i+1}: {fake.bs()}",
                'target_amount': round(random.uniform(1000, 5000), 2),
                'deadline': fake.date_between(start_date='+1m', end_date='+1y'),
                'achieved': random.choice([True, False])
            }
            
            if milestone['achieved']:
                milestone['achieved_date'] = fake.date_between(start_date='-1m', end_date='today')
            
            cursor.execute("""
                INSERT INTO goal_milestones (goal_id, title, target_amount, deadline, achieved, achieved_date)
                VALUES (%(goal_id)s, %(title)s, %(target_amount)s, %(deadline)s, %(achieved)s, %(achieved_date)s)
            """, milestone)

def generate_goal_notes(cursor, goal_ids, num_notes_per_goal=3):
    for goal_id in goal_ids:
        for _ in range(num_notes_per_goal):
            cursor.execute("""
                INSERT INTO goal_notes (goal_id, note)
                VALUES (%s, %s)
            """, (goal_id, fake.paragraph()))

def generate_savings_goal_progress(cursor, goal_ids):
    for goal_id in goal_ids:
        num_entries = random.randint(5, 10)
        start_date = datetime.now() - timedelta(days=180)
        
        for i in range(num_entries):
            entry_date = start_date + timedelta(days=i*30)
            cursor.execute("""
                INSERT INTO savings_goal_progress (goal_id, amount, date)
                VALUES (%s, %s, %s)
            """, (goal_id, round(random.uniform(100, 1000), 2), entry_date))

def generate_user_preferences(cursor, user_id):
    dashboard_widgets = json.dumps({
        'showNetWorth': True,
        'showBudgetProgress': True,
        'showRecentTransactions': True,
        'showGoalProgress': True,
        'showInvestmentSummary': True
    })
    
    visualization_preferences = json.dumps({
        'chartType': 'bar',
        'colorScheme': 'blue',
        'showLabels': True,
        'currency': 'USD'
    })
    
    notification_settings = json.dumps({
        'email': True,
        'push': True,
        'frequency': 'daily',
        'types': ['bill_due', 'goal_progress', 'budget_alert']
    })
    
    cursor.execute("""
        INSERT INTO user_preferences (user_id, theme, dashboard_widgets, 
                                    visualization_preferences, notification_settings)
        VALUES (%s, %s, %s, %s, %s)
    """, (user_id, 'light', dashboard_widgets, visualization_preferences, notification_settings))

def generate_notifications(cursor, user_id, num_notifications=10):
    notification_types = ['bill_due', 'goal_achieved', 'budget_alert', 'investment_update']
    
    for _ in range(num_notifications):
        cursor.execute("""
            INSERT INTO notifications (user_id, title, message, type, read_status)
            VALUES (%s, %s, %s, %s, %s)
        """, (
            user_id,
            fake.sentence(),
            fake.paragraph(),
            random.choice(notification_types),
            random.choice([True, False])
        ))

def generate_activity_log(cursor, user_id, num_entries=20):
    actions = ['login', 'transaction_added', 'goal_updated', 'budget_modified', 'investment_added']
    
    for _ in range(num_entries):
        details = json.dumps({
            'timestamp': fake.iso8601(),
            'browser': fake.user_agent(),
            'action_details': fake.sentence()
        })
        
        cursor.execute("""
            INSERT INTO activity_log (user_id, action, details, ip_address)
            VALUES (%s, %s, %s, %s)
        """, (
            user_id,
            random.choice(actions),
            details,
            fake.ipv4()
        ))

def main():
    try:
        # Connect to the database
        conn = connect_to_database()
        cursor = conn.cursor()
        
        # User ID for which we're generating data
        user_id = 1
        start_date = datetime(2024, 1, 1)
        
        # Generate data
        print("Generating accounts...")
        account_ids = generate_accounts(cursor, user_id)
        
        print("Generating budget categories...")
        budget_category_ids = generate_budget_categories(cursor, user_id)
        
        print("Generating transactions...")
        transaction_ids = generate_transactions(cursor, account_ids, start_date)
        
        print("Generating transaction tags...")
        tag_ids = generate_transaction_tags(cursor, user_id)
        generate_transaction_tag_relations(cursor, transaction_ids, tag_ids)
        
        print("Generating goal categories...")
        goal_category_ids = generate_goal_categories(cursor, user_id)
        
        print("Generating goals...")
        goal_ids = generate_goals(cursor, user_id, goal_category_ids)
        
        print("Generating goal milestones...")
        generate_goal_milestones(cursor, goal_ids)
        
        print("Generating goal notes...")
        generate_goal_notes(cursor, goal_ids)
        
        print("Generating savings goal progress...")
        generate_savings_goal_progress(cursor, goal_ids)
        
        print("Generating user preferences...")
        generate_user_preferences(cursor, user_id)
        
        print("Generating notifications...")
        generate_notifications(cursor, user_id)
        
        print("Generating activity log...")
        generate_activity_log(cursor, user_id)
        
        # Commit the changes
        conn.commit()
        print("Data generation completed successfully!")
        
    except mysql.connector.Error as err:
        print(f"Error: {err}")
        conn.rollback()
        
    finally:
        cursor.close()
        conn.close()

if __name__ == "__main__":
    main()