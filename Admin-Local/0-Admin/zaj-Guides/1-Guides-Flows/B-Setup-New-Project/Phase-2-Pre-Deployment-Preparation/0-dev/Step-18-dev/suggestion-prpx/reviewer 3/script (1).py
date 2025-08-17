# Let's analyze the specific scripts and their logic
import json

print("=== SCRIPT ANALYSIS: ENHANCED PERSISTENCE LOGIC ===")
print("\n1. SMART CLASSIFICATION PATTERNS:")
print("==================================")

# Read the enhanced script
with open('setup_data_persistence.sh', 'r', encoding='utf-8') as f:
    script_content = f.read()

# Extract the classification patterns
patterns = {
    'universal_exclusions': [],
    'user_patterns': [],
    'framework_detection': []
}

# Find universal exclusions pattern
exclusions_match = re.search(r'get_universal_exclusions\(\).*?echo "([^"]+)"', script_content, re.DOTALL)
if exclusions_match:
    patterns['universal_exclusions'] = exclusions_match.group(1).split()

print("Universal Build Exclusions (never persist):")
for item in patterns['universal_exclusions'][:10]:  # First 10
    print(f"  ❌ {item}")

# Find user patterns
user_patterns_section = re.search(r'detect_user_data_patterns.*?for pattern in "([^"]+)"', script_content, re.DOTALL)
if user_patterns_section:
    user_patterns_raw = user_patterns_section.group(1)
    patterns['user_patterns'] = user_patterns_raw.split()

print("\nUser Data Patterns (always persist):")
for pattern in patterns['user_patterns'][:8]:
    print(f"  ✅ {pattern}")

print("\n\n2. FRAMEWORK DETECTION LOGIC:")
print("=============================")

# Find framework detection
framework_section = re.search(r'detect_framework\(\).*?fi\n', script_content, re.DOTALL)
if framework_section:
    frameworks = re.findall(r'framework="([^"]+)".*?echo.*?(Laravel|Next\.js|React|Vue|Node\.js|Generic)', framework_section.group(0))
    for fw_name, detected in frameworks:
        print(f"  🎯 {detected}: {fw_name}")

print("\n\n3. CORE PROBLEMS BEING SOLVED:")
print("==============================")

# Read the CodeCanyon specifics document
with open('CodeCanyon_Specifics.md', 'r', encoding='utf-8') as f:
    codecanyon_content = f.read()

problems = [
    "Demo data preservation vs user data",
    "Vendor asset updates vs custom uploads",
    "Shared hosting symlink limitations", 
    "CodeCanyon directory structure variations",
    "Zero downtime with data persistence",
    "First deploy vs subsequent deploy logic"
]

for i, problem in enumerate(problems, 1):
    print(f"  {i}. {problem}")

print("\n\n4. SOCIETYPAL REAL EXAMPLE:")
print("===========================")

# Extract the SocietyPal example structure
society_example = re.search(r'# RESULT: SocietyPal Structure.*?(?=# 3\.|$)', summary_content, re.DOTALL)
if society_example:
    lines = society_example.group(0).split('\n')[:15]
    for line in lines:
        if line.strip() and not line.startswith('#'):
            print(f"  {line}")
        elif line.strip().startswith('# '):
            print(f"{line}")