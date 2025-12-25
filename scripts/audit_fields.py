import re
import os
import json
from pathlib import Path

# Map each migration file to its corresponding view and request
table_mappings = {
    'teacher_profiles': {
        'view': 'resources/views/teachers/index.blade.php',
        'request': 'app/Http/Requests/User/TeacherRequest.php',
        'model': 'app/Models/TeacherProfile.php'
    },
    'students': {
       'view': 'resources/views/students/index.blade.php',
        'request': 'app/Http/Requests/User/UserRequest.php',
        'model': 'app/Models/Student.php'
    },
    'classes': {
        'view': 'resources/views/classes/index.blade.php',
        'request': 'app/Http/Requests/Class/ClassRequest.php',
        'model': 'app/Models/ClassRoom.php'
    },
    'assignments': {
        'view': 'resources/views/assignments/index.blade.php',
        'request': 'app/Http/Requests/Academic/AssignmentRequest.php',
        'model': 'app/Models/Assignment.php'
    },
    'fee_types': {
        'view': 'resources/views/fee-types/index.blade.php',
        'request': 'app/Http/Requests/Payment/FeeTypeRequest.php',
        'model': 'app/Models/FeeType.php'
    }
}

def extract_migration_fields(migration_content):
    """Extract field names from migration file"""
    fields = []
    # Match $table->fieldType('fieldName') patterns
    pattern = r'\$table->(?:string|text|integer|bigInteger|date|datetime|boolean|decimal|enum|foreignId|uuid)\([\'"](\w+)[\'"]'
    matches = re.findall(pattern, migration_content)
    return matches

def extract_form_fields(view_content):
    """Extract field names from Blade form inputs"""
    fields = []
    # Match name="fieldName" patterns
    pattern = r'name=["\'](\w+)["\']'
    matches = re.findall(pattern, view_content)
    return list(set(matches))  # Remove duplicates

def extract_validation_rules(request_content):
    """Extract field names from validation rules"""
    fields = []
    # Match array keys in validation rules
    pattern = r'["\'](\w+)["\']\s*=>'
    matches = re.findall(pattern, request_content)
    return list(set(matches))

def audit_table(table_name, mapping, base_path):
    """Audit a single table for field coverage"""
    print(f"\n{'='*60}")
    print(f"Auditing: {table_name}")
    print(f"{'='*60}")
    
    results = {
        'table': table_name,
        'migration_fields': [],
        'form_fields': [],
        'validation_fields': [],
        'missing_from_form': [],
        'missing_from_validation': []
    }
    
    # Read migration
    migration_path = Path(base_path) / f"database/migrations"
    migration_file = list(migration_path.glob(f"*create_{table_name}_table.php"))
    
    if not migration_file:
        print(f"⚠️  Migration not found for {table_name}")
        return results
    
    with open(migration_file[0], 'r', encoding='utf-8') as f:
        migration_content = f.read()
        results['migration_fields'] = extract_migration_fields(migration_content)
    
    # Read view
    view_path = Path(base_path) / mapping.get('view', '')
    if view_path.exists():
        with open(view_path, 'r', encoding='utf-8') as f:
            view_content = f.read()
            results['form_fields'] = extract_form_fields(view_content)
    
    # Read request
    request_path = Path(base_path) / mapping.get('request', '')
    if request_path.exists():
        with open(request_path, 'r', encoding='utf-8') as f:
            request_content = f.read()
            results['validation_fields'] = extract_validation_rules(request_content)
    
    # Auto-excluded fields (managed by system)
    auto_fields = ['id', 'created_at', 'updated_at', 'deleted_at', 'school_id', 'user_id']
    
    # Find missing fields
    for field in results['migration_fields']:
        if field not in auto_fields:
            if field not in results['form_fields']:
                results['missing_from_form'].append(field)
            if field not in results['validation_fields']:
                results['missing_from_validation'].append(field)
    
    # Print results
    print(f"📋 Migration Fields ({len(results['migration_fields'])}): {', '.join(results['migration_fields'])}")
    print(f"📝 Form Fields ({len(results['form_fields'])}): {','.join(results['form_fields'])}")
    print(f"✅ Validation Rules ({len(results['validation_fields'])}): {', '.join(results['validation_fields'])}")
    
    if results['missing_from_form']:
        print(f"❌ Missing from Form: {', '.join(results['missing_from_form'])}")
    else:
        print("✅ All fields covered in form")
    
    if results['missing_from_validation']:
        print(f"❌ Missing from Validation: {', '.join(results['missing_from_validation'])}")
    else:
        print("✅ All fields have validation rules")
    
    return results

# Run audit
base_path = r"c:\Users\pc\Videos\eschool1"
all_results = []

for table_name, mapping in table_mappings.items():
    result = audit_table(table_name, mapping, base_path)
    all_results.append(result)

# Summary
print(f"\n{'='*60}")
print("AUDIT SUMMARY")
print(f"{'='*60}")

tables_with_issues = [r for r in all_results if r['missing_from_form'] or r['missing_from_validation']]
if tables_with_issues:
    print(f"⚠️  {len(tables_with_issues)} table(s) have missing fields:")
    for result in tables_with_issues:
        print(f"  - {result['table']}")
else:
    print("✅ All audited tables have complete field coverage!")
