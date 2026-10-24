document.addEventListener('DOMContentLoaded', () => {
    const steps = document.querySelectorAll('.step');
    const nextBtns = document.querySelectorAll('.next-btn');
    const prevBtns = document.querySelectorAll('.prev-btn');
    const progressBar = document.getElementById('progress-bar');
    const form = document.getElementById('wizard-form');
    const loader = document.getElementById('loader');
    const successMsg = document.getElementById('success-msg');
    let currentStep = 0;

    function updateView() {
        steps.forEach((step, index) => {
            if(index === currentStep) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });
        progressBar.style.width = ((currentStep + 1) / steps.length) * 100 + '%';
    }

    nextBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const inputs = steps[currentStep].querySelectorAll('input[required], select[required], textarea[required]');
            let valid = true;
            inputs.forEach(input => {
                if(!input.checkValidity()) {
                    input.reportValidity();
                    valid = false;
                }
            });
            if (valid && currentStep < steps.length - 1) {
                currentStep++;
                updateView();
            }
        });
    });

    prevBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                updateView();
            }
        });
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        steps.forEach(s => s.classList.add('hidden'));
        document.getElementById('submit-btn').classList.add('hidden');
        document.querySelectorAll('.prev-btn').forEach(b => b.classList.add('hidden'));
        progressBar.parentElement.classList.add('hidden');
        loader.classList.remove('hidden');

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('api/new-project.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            
            loader.classList.add('hidden');
            successMsg.classList.remove('hidden');
            
            if(result.success && result.ia_data) {
                document.getElementById('ai-summary').innerText = `"${result.ia_data.resumo_curto}"`;
            } else {
                document.getElementById('ai-summary').innerText = "Projeto salvo, mas a análise de IA demorou um pouco.";
            }

        } catch (error) {
            console.error(error);
            alert("Erro ao enviar o projeto. Tente novamente.");
            location.reload();
        }
    });
});
