import numpy as np
import matplotlib.pyplot as plt
from scipy import stats

print("Roll No: 16010124161")
print("Name: Zain Lakhani")

n = 200
x_bar = 6.5
mu_0 = 7
sigma_sq = 8.5
sigma = np.sqrt(sigma_sq)
alpha = 0.05

print("\nQ1")
print("H0: mu = 7")
print("H1: mu != 7")

z = (x_bar - mu_0) / (sigma / np.sqrt(n))
print("Z-stat:", z)

z_crit = stats.norm.ppf(1 - alpha/2)
print("Critical:", z_crit)

if abs(z) > z_crit:
    print("Reject H0")
else:
    print("Fail to Reject H0")

x = np.linspace(-4, 4, 1000)
y = stats.norm.pdf(x)

plt.figure(figsize=(8,4))
plt.plot(x, y, label='Normal Curve')
plt.fill_between(x, y, where=(x <= -z_crit) | (x >= z_crit), alpha=0.3, label='Rejection Region')
plt.axvline(z, linestyle='--', label=f'Test Statistic: {z:.3f}')
plt.axvline(z_crit, label=f'Critical: {z_crit:.3f}')
plt.axvline(-z_crit)
plt.title('Two-tailed Z-test')
plt.xlabel('Z value')
plt.ylabel('Density')
plt.legend()
plt.grid(True)
plt.show()
