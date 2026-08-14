# Enterprise Cloud Workspace (IaC)

![Ansible](https://img.shields.io/badge/ansible-%231A1918.svg?style=for-the-badge&logo=ansible&logoColor=white)
![Kubernetes](https://img.shields.io/badge/kubernetes-%23326ce5.svg?style=for-the-badge&logo=kubernetes&logoColor=white)
![Nextcloud](https://img.shields.io/badge/Nextcloud-0082c9?style=for-the-badge&logo=Nextcloud&logoColor=white)

This repository contains the Infrastructure-as-Code (IaC) playbooks required to deploy a fully modular, Highly Available Enterprise Cloud Workspace. 

The stack is orchestrated via Ansible and deployed dynamically into a Kubernetes (K8S/K3S) environment.

## 🏗️ Architecture Overview

The platform is completely modularized into isolated Ansible Roles. The core architecture relies on:
- **Core Platform:** Nextcloud Hub
- **Database Layer:** PostgreSQL & Redis Cache (Stateless offload)
- **Ingress / Routing:** Traefik Ingress Controller with automatic Let's Encrypt TLS
- **Document Editors (Toggleable):** Collabora CODE, OnlyOffice, or Euro-Office
- **Video Conferencing:** Jitsi Meet (Optional)

## 📁 Repository Structure

We enforce a strict, modular role-based architecture. Application deployments are decoupled, allowing administrators to hot-swap components by simply modifying the master playbook.

```text
.
├── group_vars/            # Environment specific configurations
├── playbooks/             # Master execution playbooks
│   ├── deploy.yaml        # The primary cluster deployment playbook
│   └── destroy.yaml       # Teardown playbook
└── roles/                 # Isolated application deployment logic
    ├── collabora/         # Collabora Office deployment
    ├── euro-office/       # Euro-Office deployment
    ├── jitsi/             # Jitsi Video Conferencing
    ├── nextcloud/         # Core Nextcloud Platform
    └── onlyoffice/        # OnlyOffice deployment
```

## 🚀 Quick Start & Usage

### 1. Prerequisites
Ensure you have the following installed on your control node:
* `ansible` (v2.9+)
* `kubectl`
* A running Kubernetes cluster (K3S recommended for edge/lean environments) with a valid `/etc/rancher/k3s/k3s.yaml` or standard `kubeconfig`.

### 2. Configure Modules
To select which applications you want to deploy, simply open the master playbook and uncomment the desired roles.

**File:** `playbooks/deploy.yaml`
```yaml
  roles:
    - nextcloud            # Always required
    - collabora            # Uncomment your preferred office suite
    # - onlyoffice
    # - euro-office
```

### 3. Deploy the Stack
Once your roles are selected and your inventory is configured (`inventory.ini`), execute the playbook:

```bash
ansible-playbook -i inventory.ini playbooks/deploy.yaml
```

## 🛡️ Security & Compliance
All pods are deployed into dedicated Kubernetes namespaces to enforce strict logical isolation. Network traffic is routed exclusively through the Ingress controller to minimize attack surfaces.
