default:
    @just --list

# Deploy or update Nextcloud (Safe to run anytime)
deploy:
    ansible-playbook playbooks/deploy.yaml

# Completely destroy the Nextcloud application (DOWNTIME)
destroy:
    ansible-playbook playbooks/destroy.yaml

# Run the database backup process
backup:
    ansible-playbook playbooks/backup.yaml

# Securely push the repository to Git without leaking credentials
push message="Automated update":
    ./push.sh "{{message}}"
